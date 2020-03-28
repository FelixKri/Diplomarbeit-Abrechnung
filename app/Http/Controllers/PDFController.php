<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App\PrescribingSuggestion;
use App\Invoice;

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
      $pdf->getDomPDF()->set_option("enable_php", true);
      
      return $pdf->download($prescribing->title.".pdf");
    }

    public function downloadInvoiceById($id){
      $invoice = Invoice::with('author', 'positions', 'positions.userHasInvoicePosition', 'positions.userHasInvoicePosition.user', 'positions.userHasInvoicePosition.user.group')->find($id);

      $pdf = PDF::loadView('pdfs.invoicePDF', compact('invoice'));
      $pdf->getDomPDF()->set_option("enable_php", true);

      return $pdf->download($invoice->reason.".pdf");
    }
}
