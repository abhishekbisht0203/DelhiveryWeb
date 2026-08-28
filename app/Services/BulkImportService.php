<?php

namespace App\Services;

use App\Models\Import;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BulkImportService
{
    protected ShipmentService $shipmentService;
    protected AwbService $awbService;

    public function __construct(ShipmentService $shipmentService, AwbService $awbService)
    {
        $this->shipmentService = $shipmentService;
        $this->awbService = $awbService;
    }

    public function processImport(Import $import, User $user): array
    {
        $filePath = $import->file_path;

        if (!file_exists($filePath)) {
            throw new \RuntimeException('Import file not found');
        }

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            throw new \RuntimeException('Unable to open import file');
        }

        $headers = fgetcsv($handle);
        $headers = array_map('strtolower', array_map('trim', $headers));

        $totalRows = 0;
        $successRows = 0;
        $errorRows = 0;
        $errors = [];
        $shipmentsData = [];

        while (($row = fgetcsv($handle)) !== false) {
            $totalRows++;
            $rowData = array_combine($headers, $row);

            $validationErrors = $this->validateRow($rowData, $totalRows);

            if (!empty($validationErrors)) {
                $errorRows++;
                $errors[] = [
                    'row'    => $totalRows,
                    'errors' => $validationErrors,
                    'data'   => $rowData,
                ];
                continue;
            }

            $shipmentsData[] = $this->mapRowToShipmentData($rowData, $user);
        }

        fclose($handle);

        if (!empty($shipmentsData)) {
            $result = $this->shipmentService->bulkCreateShipments($shipmentsData, $user);
            $successRows = $result['success'];
            $errorRows += $result['failed'];
            $errors = array_merge($errors, $result['errors']);
        }

        $import->update([
            'total_rows'    => $totalRows,
            'success_rows'  => $successRows,
            'error_rows'    => $errorRows,
            'status'        => 'completed',
            'processed_at'  => now(),
        ]);

        if (!empty($errors)) {
            $import->update(['errors' => $errors]);
        }

        return [
            'total'   => $totalRows,
            'success' => $successRows,
            'failed'  => $errorRows,
            'errors'  => $errors,
        ];
    }

    public function validateRow(array $row, int $rowNumber): array
    {
        $errors = [];
        $requiredFields = ['customer_name', 'customer_phone', 'customer_address', 'customer_city', 'customer_state', 'customer_pincode'];

        foreach ($requiredFields as $field) {
            if (empty($row[$field])) {
                $errors[] = "Row {$rowNumber}: '{$field}' is required";
            }
        }

        if (!empty($row['customer_phone']) && !preg_match('/^\d{10}$/', $row['customer_phone'])) {
            $errors[] = "Row {$rowNumber}: Invalid phone number format";
        }

        if (!empty($row['customer_pincode']) && !preg_match('/^\d{6}$/', $row['customer_pincode'])) {
            $errors[] = "Row {$rowNumber}: Invalid pincode format";
        }

        if (!empty($row['payment_type']) && !in_array($row['payment_type'], ['prepaid', 'cod'])) {
            $errors[] = "Row {$rowNumber}: Invalid payment type";
        }

        if (!empty($row['cod_amount']) && $row['payment_type'] !== 'cod') {
            $errors[] = "Row {$rowNumber}: COD amount provided but payment type is not COD";
        }

        if (!empty($row['customer_email']) && !filter_var($row['customer_email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Row {$rowNumber}: Invalid email format";
        }

        return $errors;
    }

    public function generateErrorReport(Import $import): StreamedResponse
    {
        $errors = $import->errors ?? [];

        return response()->stream(function () use ($errors) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Row', 'Error', 'Data']);

            foreach ($errors as $error) {
                fputcsv($handle, [
                    $error['row'],
                    implode('; ', is_array($error['errors']) ? $error['errors'] : [$error['errors']]),
                    json_encode($error['data'] ?? []),
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"import_errors_{$import->id}.csv\"",
        ]);
    }

    protected function mapRowToShipmentData(array $row, User $user): array
    {
        return [
            'order_id'         => $row['order_id'] ?? null,
            'merchant_id'      => $user->merchant_id ?? $user->id,
            'customer_name'    => $row['customer_name'],
            'customer_phone'   => $row['customer_phone'],
            'customer_email'   => $row['customer_email'] ?? null,
            'customer_address' => $row['customer_address'],
            'customer_city'    => $row['customer_city'],
            'customer_state'   => $row['customer_state'],
            'customer_pincode' => $row['customer_pincode'],
            'payment_type'     => $row['payment_type'] ?? 'prepaid',
            'cod_amount'       => (float) ($row['cod_amount'] ?? 0),
            'declared_value'   => (float) ($row['declared_value'] ?? 0),
            'weight'           => (float) ($row['weight'] ?? 0),
            'length'           => (float) ($row['length'] ?? 0),
            'width'            => (float) ($row['width'] ?? 0),
            'height'           => (float) ($row['height'] ?? 0),
            'pieces'           => (int) ($row['pieces'] ?? 1),
            'description'      => $row['description'] ?? null,
        ];
    }
}
