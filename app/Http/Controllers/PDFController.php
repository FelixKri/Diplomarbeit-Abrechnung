<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App\PrescribingSuggestion;

class PDFController extends Controller
{
    public function dowloadTestPDF()
    {
    	$pdf = PDF::loadView('pdfs.pdfView');
		  return $pdf->download('invoice.pdf');
    }

    public function downloadPrescribingById($id){
      $prescribing = PrescribingSuggestion::with('author', 'positions', 'reason', 'positions.user', 'positions.user.group')->find($id);
      $pdf = PDF::loadView('pdfs.prescribingPDF', compact('prescribing'));

      return $pdf->download($prescribing->title.".pdf");
    }
}
