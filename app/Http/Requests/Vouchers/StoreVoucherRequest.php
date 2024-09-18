<?php

namespace App\Http\Requests\Vouchers;

use Illuminate\Foundation\Http\FormRequest;

class StoreVoucherRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'files' => ['required', function ($attribute, $value, $fail) {
                if (is_array($value)) return;
                if (!$value instanceof \Illuminate\Http\UploadedFile) {
                    $fail("$attribute must be a file or an array of files.");
                    return;
                }
                if ($value->getMimeType() !== 'text/xml') {
                    $fail("$attribute must be an XML file.");
                }
            }],
            'files.*' => ['file', 'mimes:xml'],
        ];
    }


    public function getFilesForProcessing(): array
    {
        $xmlFiles = $this->file('files');
        if (!is_array($xmlFiles)) $xmlFiles = [$xmlFiles];

        $vouchersForProcessing = [];
        foreach ($xmlFiles as $xmlFile) {
            $vouchersForProcessing[] = [
                'filename' => $xmlFile->getClientOriginalName() ?? $xmlFile->getFilename(),
                'content' => file_get_contents($xmlFile->getRealPath()),
            ];
        }
        return $vouchersForProcessing;
    }
}
