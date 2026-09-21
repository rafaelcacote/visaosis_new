<?php

namespace App\Helpers;

use Barryvdh\DomPDF\PDF as PdfDocument;

class PdfHelper
{
    /**
     * Renders the PDF and stamps "Página X de Y" centered in the footer area of every page.
     */
    public static function addPageNumbers(PdfDocument $pdf, string $font = 'helvetica', float $size = 8): void
    {
        $pdf->render();

        $canvas = $pdf->getDomPDF()->getCanvas();
        $fontMetrics = $pdf->getDomPDF()->getFontMetrics();
        $resolvedFont = $fontMetrics->getFont($font);
        $color = [0.42, 0.45, 0.5];

        $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) use ($resolvedFont, $size, $color) {
            $text = "Página {$pageNumber} de {$pageCount}";
            $width = $fontMetrics->getTextWidth($text, $resolvedFont, $size);
            $x = ($canvas->get_width() - $width) / 2;
            $y = $canvas->get_height() - 40;
            $canvas->text($x, $y, $text, $resolvedFont, $size, $color);
        });
    }
}
