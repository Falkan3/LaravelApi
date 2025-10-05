<?php

namespace App\Http\Resources;

use Illuminate\Http\Request as Request;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class BaseResource extends JsonResource {
    protected array $settings = [];
    private string $signature = 'resource';

    /**
     * BaseResource constructor.
     */
    public function __construct($resource, array $settings = []) {
        parent::__construct($resource);
        $this->settings = $settings;
    }

    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array {
        return parent::toArray($request);
    }

    /**
     * Transform the resource into a formatted list array.
     *
     * @return array|null
     */
    public abstract function toList(): ?array;

    /**
     * Transform the resource into csv formatted array.
     */
    public function toCsv(array $headings): array {
        $filename = $this->signature . '_' . date('Y-m-d H:i:s');
        $destinationFolder = '';
        return ['filename' => $filename, 'path' => $destinationFolder . '/' . $filename, 'headings' => $headings, 'rows' => $this->resource];
    }

    /**
     * Transform the resource into csv formatted array.
     */
    public function toCsvTransform(string $headingsKey = 'headings', string $dataKey = 'data'): array {
        $filename = $this->signature . '_' . date('Y-m-d H:i:s');
        $destinationFolder = '';
        // generate a CSV file using the resource data
        $headings = array_column($this->resource, $headingsKey);
        $rows = array_column($this->resource, $dataKey);
        return ['filename' => $filename, 'path' => $destinationFolder . '/' . $filename, 'headings' => $headings, 'rows' => $rows];
    }

    /**
     * Get simulated dummy data for testing.
     */
    public abstract function getDummyData(Request $request): array;

    public function settingExists(string $settingName): bool {
        return isset($this->settings[$settingName]) && $this->settings[$settingName] !== null;
    }
}
