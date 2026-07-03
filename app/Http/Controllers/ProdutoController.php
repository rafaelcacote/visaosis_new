<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Produto;
use App\Models\Categoria;
use App\Models\ProductImage;

class ProdutoController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'search' => trim((string) $request->get('search', '')),
            'categoria' => trim((string) $request->get('categoria', '')),
            'status' => trim((string) $request->get('status', '')),
        ];

        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);

        $locationIds = [];
        if ($tenantId) {
            $locationIds = collect($userLocations)
                ->where('tenant_id', $tenantId)
                ->pluck('location_id')
                ->toArray();
        } elseif ($locationId) {
            $locationIds = [$locationId];
        }

        if (empty($tenantId)) {
            $produtos = Produto::whereRaw('1 = 0')->paginate(15);
            $categorias = collect();
            return view('produtos.index', compact('produtos', 'categorias', 'filters'));
        }

        $query = Produto::query()
            ->where('tenant_id', $tenantId)
            ->with('categoria');

        if (!empty($locationIds)) {
            $query->where(function ($q) use ($locationIds) {
                $q->whereIn('location_id', $locationIds)
                    ->orWhereNull('location_id');
            });
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'ILIKE', "%{$search}%")
                    ->orWhere('marca', 'ILIKE', "%{$search}%");
            });
        }

        if ($filters['categoria'] !== '') {
            $categoriaId = (int) $filters['categoria'];
            $query->where('categoria_id', $categoriaId)
                ->whereHas('categoria', function ($categoriaQuery) use ($tenantId, $locationIds, $categoriaId) {
                    $categoriaQuery->where('id', $categoriaId)
                        ->where('tenant_id', $tenantId);

                    if (!empty($locationIds)) {
                        $categoriaQuery->where(function ($q) use ($locationIds) {
                            $q->whereIn('location_id', $locationIds)
                                ->orWhereNull('location_id');
                        });
                    }
                });
        }

        if ($filters['status'] !== '') {
            if (in_array($filters['status'], ['1', 'ativo'], true)) {
                $query->where('ativo', true);
            } elseif (in_array($filters['status'], ['0', 'inativo'], true)) {
                $query->where('ativo', false);
            }
        }

        $produtos = $query->orderBy('nome')->paginate(5);

        $categorias = Categoria::query()
            ->where('tenant_id', $tenantId)
            ->where('ativo', true)
            ->when(!empty($locationIds), function ($query) use ($locationIds) {
                $query->where(function ($q) use ($locationIds) {
                    $q->whereIn('location_id', $locationIds)
                        ->orWhereNull('location_id');
                });
            })
            ->orderBy('descricao')
            ->get();

        return view('produtos.index', compact('produtos', 'categorias', 'filters'));
    }

    public function create()
    {
        $categorias = $this->getCategoriasAtivas();
        return view('produtos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $locationId = session('location_id');
        $tenantId = session('tenant_id');
        $userLocations = session('user_locations', []);

        if (!$locationId && $tenantId) {
            $firstLocation = collect($userLocations)->where('tenant_id', $tenantId)->first();
            $locationId = $firstLocation['location_id'] ?? null;
        }

        if (!$tenantId) {
            return back()->withInput()
                ->with('error', 'Nenhum tenant disponível para criar o produto.');
        }

        $request->validate([
            'nome' => [
                'required',
                'string',
                'max:160',
                function ($attribute, $value, $fail) use ($tenantId) {
                    $exists = Produto::whereRaw('LOWER(nome) = ?', [strtolower($value)])
                        ->where('tenant_id', $tenantId)
                        ->whereNull('deleted_at')
                        ->exists();
                    if ($exists) {
                        $fail('Já existe um produto com este nome.');
                    }
                },
            ],
            'categoria_id' => 'required|exists:categoria_produto,id',
            'preco_venda' => 'required|numeric|min:0',
            'preco_custo' => 'nullable|numeric|min:0',
            'marca' => 'nullable|string|max:100',
            'ativo' => 'nullable|boolean',
            'new_images' => 'nullable|array',
        ], [
            'nome.required' => 'O nome do produto é obrigatório.',
            'nome.max' => 'O nome não pode ter mais de 160 caracteres.',
            'categoria_id.required' => 'A categoria é obrigatória.',
            'categoria_id.exists' => 'Categoria inválida.',
            'preco_venda.required' => 'O preço de venda é obrigatório.',
            'preco_venda.numeric' => 'O preço de venda deve ser um número.',
        ]);

        $atributos = $this->buildAtributosFromRequest($request);

        try {
            DB::beginTransaction();

            $produto = Produto::create([
                'tenant_id' => $tenantId,
                'location_id' => $locationId,
                'user_id' => Auth::id(),
                'nome' => $request->nome,
                'categoria_id' => $request->categoria_id,
                'marca' => $request->marca ?: null,
                'atributos' => $atributos,
                'preco_custo' => $request->preco_custo ?: null,
                'preco_venda' => $request->preco_venda,
                'ativo' => $request->has('ativo') && $request->ativo == '1',
            ]);

            $this->syncProductImages($request, $produto);

            DB::commit();

            return redirect()->route('produtos.index')
                ->with('success', 'Produto cadastrado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Erro ao cadastrar produto: ' . $e->getMessage());
        }
    }

    public function show(Produto $produto)
    {
        $this->checkTenantAccess($produto);
        $produto->load([
            'categoria',
            'images' => function ($query) {
                $query->whereNull('deleted_at')
                    ->orderByDesc('principal')
                    ->orderBy('ordem')
                    ->orderBy('id');
            },
        ]);
        return view('produtos.show', compact('produto'));
    }

    public function edit(Produto $produto)
    {
        $this->checkTenantAccess($produto);
        $produto->load([
            'images' => function ($query) {
                $query->whereNull('deleted_at')
                    ->orderBy('ordem')
                    ->orderBy('id');
            },
        ]);
        $categorias = $this->getCategoriasAtivas();
        return view('produtos.edit', compact('produto', 'categorias'));
    }

    public function update(Request $request, Produto $produto)
    {
        $this->checkTenantAccess($produto);

        $request->validate([
            'nome' => [
                'required',
                'string',
                'max:160',
                function ($attribute, $value, $fail) use ($produto) {
                    $exists = Produto::whereRaw('LOWER(nome) = ?', [strtolower($value)])
                        ->where('tenant_id', $produto->tenant_id)
                        ->where('id', '!=', $produto->id)
                        ->whereNull('deleted_at')
                        ->exists();
                    if ($exists) {
                        $fail('Já existe um produto com este nome.');
                    }
                },
            ],
            'categoria_id' => 'required|exists:categoria_produto,id',
            'preco_venda' => 'required|numeric|min:0',
            'preco_custo' => 'nullable|numeric|min:0',
            'marca' => 'nullable|string|max:100',
            'ativo' => 'nullable|boolean',
            'new_images' => 'nullable|array',
            'existing_images' => 'nullable|array',
        ], [
            'nome.required' => 'O nome do produto é obrigatório.',
            'nome.max' => 'O nome não pode ter mais de 160 caracteres.',
            'categoria_id.required' => 'A categoria é obrigatória.',
            'categoria_id.exists' => 'Categoria inválida.',
            'preco_venda.required' => 'O preço de venda é obrigatório.',
        ]);

        $atributos = $this->buildAtributosFromRequest($request);

        try {
            DB::beginTransaction();

            $produto->update([
                'nome' => $request->nome,
                'categoria_id' => $request->categoria_id,
                'marca' => $request->marca ?: null,
                'atributos' => $atributos,
                'preco_custo' => $request->preco_custo ?: null,
                'preco_venda' => $request->preco_venda,
                'ativo' => $request->has('ativo') && $request->ativo == '1',
            ]);

            $produto->refresh();
            $this->syncProductImages($request, $produto);

            DB::commit();

            return redirect()->route('produtos.index')
                ->with('success', 'Produto atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Erro ao atualizar produto: ' . $e->getMessage());
        }
    }

    public function destroy(Produto $produto)
    {
        $this->checkTenantAccess($produto);

        try {
            DB::beginTransaction();
            $produto->delete();
            DB::commit();

            return redirect()->route('produtos.index')
                ->with('success', 'Produto excluído com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            if (
                strpos($e->getMessage(), 'Foreign key violation') !== false ||
                strpos($e->getMessage(), 'foreign key constraint') !== false
            ) {
                return back()->with('error', 'Não é possível excluir este produto porque está sendo utilizado.');
            }

            return back()->with('error', 'Erro ao excluir produto: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Produto $produto)
    {
        $this->checkTenantAccess($produto);

        try {
            DB::beginTransaction();
            $novoStatus = !$produto->ativo;
            $produto->update(['ativo' => $novoStatus]);
            DB::commit();

            return response()->json([
                'success' => true,
                'status' => $novoStatus,
                'message' => 'Status atualizado com sucesso!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar status: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function getCategoriasAtivas()
    {
        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);

        $locationIds = [];
        if ($tenantId) {
            $locationIds = collect($userLocations)
                ->where('tenant_id', $tenantId)
                ->pluck('location_id')
                ->toArray();
        } elseif ($locationId) {
            $locationIds = [$locationId];
        }

        if (empty($locationIds)) {
            return Categoria::whereRaw('1 = 0')->get();
        }

        return Categoria::whereIn('location_id', $locationIds)
            ->where('ativo', true)
            ->orderBy('descricao')
            ->get();
    }

    private function buildAtributosFromRequest(Request $request): array
    {
        $atributos = [];
        if ($request->has('attr_keys') && $request->has('attr_values')) {
            $keys = $request->input('attr_keys', []);
            $values = $request->input('attr_values', []);
            foreach ($keys as $index => $key) {
                $key = is_string($key) ? trim($key) : '';
                $value = isset($values[$index]) ? trim((string) $values[$index]) : '';
                if ($key !== '' && $value !== '') {
                    $atributos[$key] = $value;
                }
            }
        }
        return $atributos;
    }

    private function checkTenantAccess(Produto $produto)
    {
        $tenantId = session('tenant_id');
        if (!$tenantId || (int) $produto->tenant_id !== (int) $tenantId) {
            abort(403, 'Acesso negado. Este produto não pertence ao seu tenant.');
        }

        $locationId = session('location_id');
        $userLocations = session('user_locations', []);
        $locationIds = [];
        if ($tenantId) {
            $locationIds = collect($userLocations)
                ->where('tenant_id', $tenantId)
                ->pluck('location_id')
                ->toArray();
        } elseif ($locationId) {
            $locationIds = [$locationId];
        }

        if (!empty($locationIds) && $produto->location_id !== null && !in_array($produto->location_id, $locationIds)) {
            abort(403, 'Acesso negado. Este produto não pertence à sua localização.');
        }
    }

    private function syncProductImages(Request $request, Produto $produto): void
    {
        $existingImages = $produto->images()->get()->keyBy('id');
        $processedExisting = collect();
        $desiredPrimaryId = null;
        $hasExistingPayload = $request->has('existing_images');

        if ($hasExistingPayload) {
            foreach ($request->input('existing_images', []) as $imageId => $meta) {
                if (!$existingImages->has($imageId)) {
                    continue;
                }

                $image = $existingImages->get($imageId);
                $meta = is_array($meta) ? $meta : [];

                if ($this->shouldRemoveImage($meta)) {
                    $this->deleteProductImage($image);
                    continue;
                }

                $image->ordem = (int) ($meta['order'] ?? $image->ordem ?? 0);
                $isPrincipal = $this->toBool($meta['principal'] ?? false);
                $image->principal = $isPrincipal;
                $image->ativo = array_key_exists('ativo', $meta) ? $this->toBool($meta['ativo']) : true;
                $image->save();

                if ($isPrincipal) {
                    $desiredPrimaryId = $image->id;
                }

                $processedExisting->push($image->id);
            }

            $existingImages->keys()
                ->diff($processedExisting)
                ->each(function ($imageId) use ($existingImages) {
                    $this->deleteProductImage($existingImages->get($imageId));
                });
        }

        foreach ($request->input('new_images_meta', []) as $tempId => $meta) {
            $file = $request->file("new_images.$tempId");

            if (!$file) {
                continue;
            }

            $meta = is_array($meta) ? $meta : [];
            $order = (int) ($meta['order'] ?? 0);
            $isPrincipal = $this->toBool($meta['principal'] ?? false);

            $image = $this->storeProductImage($produto, $file, $order, $isPrincipal);

            if ($isPrincipal) {
                $desiredPrimaryId = $image->id;
            }
        }

        $this->normalizeImageOrder($produto);
        $this->applyPrimaryImage($produto, $desiredPrimaryId);
    }

    private function shouldRemoveImage(array $meta): bool
    {
        return array_key_exists('remove', $meta) && $this->toBool($meta['remove']);
    }

    private function toBool($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
    }

    private function storeProductImage(Produto $produto, $file, int $order, bool $isPrincipal): ProductImage
    {
        $tenantId = session('tenant_id') ?? $produto->tenant_id;
        $userId = Auth::id();

        $directory = $tenantId
            ? "tenants/{$tenantId}/products/{$produto->id}"
            : "products/{$produto->id}";

        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        $safeBase = Str::slug($baseName) ?: 'imagem-produto';
        $fileName = "{$safeBase}-" . Str::uuid() . '.' . $extension;

        $storedPath = $file->storeAs($directory, $fileName, 'public');

        return $produto->images()->create([
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'nome_arquivo' => $originalName,
            'caminho_arquivo' => $storedPath,
            'ordem' => $order,
            'principal' => $isPrincipal,
            'ativo' => true,
        ]);
    }

    private function deleteProductImage(ProductImage $image): void
    {
        if ($image->caminho_arquivo) {
            Storage::disk('public')->delete($image->caminho_arquivo);
        }

        $image->delete();
    }

    private function normalizeImageOrder(Produto $produto): void
    {
        $images = $produto->images()
            ->whereNull('deleted_at')
            ->orderBy('ordem')
            ->orderBy('id')
            ->get()
            ->values();

        foreach ($images as $index => $image) {
            if ((int) $image->ordem !== $index) {
                $image->ordem = $index;
                $image->save();
            }
        }
    }

    private function applyPrimaryImage(Produto $produto, ?int $desiredPrimaryId): void
    {
        $images = $produto->images()
            ->whereNull('deleted_at')
            ->orderBy('ordem')
            ->orderBy('id')
            ->get();

        if ($images->isEmpty()) {
            return;
        }

        if (!$desiredPrimaryId || !$images->contains('id', $desiredPrimaryId)) {
            $desiredPrimaryId = $images->first()->id;
        }

        foreach ($images as $image) {
            $shouldBePrimary = $image->id === $desiredPrimaryId;
            if ((bool) $image->principal !== $shouldBePrimary) {
                $image->principal = $shouldBePrimary;
                $image->save();
            }
        }
    }
}
