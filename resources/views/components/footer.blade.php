<!-- Location Selector Modal -->
@include('components.location-selector-modal')

<footer class="footer">
  <div class="footer-inner-wraper">
    <div class="d-sm-flex justify-content-center justify-content-sm-between py-2">
      <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
        {{ config('app.name', 'VisaoSis') }} &copy; {{ date('Y') }}. Todos os direitos reservados.
      </span>
      <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center text-muted">
        Sistema de gestao financeira e administrativa.
      </span>
    </div>
  </div>
</footer>