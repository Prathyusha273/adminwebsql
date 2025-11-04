<?php

namespace App\Traits;

use Carbon\Carbon;

trait FormatsDatesTrait
{
    /**
     * Format a date for display in DataTables or JSON responses
     * 
     * @param string|null $date
     * @param string $format Default: 'd/m/Y H:i' (31/12/2025 18:29)
     * @return string
     */
    protected function formatDate($date, $format = 'd/m/Y H:i')
    {
        if (!$date) {
            return '-';
        }

        try {
            return Carbon::parse($date)->format($format);
        } catch (\Exception $e) {
            return '-';
        }
    }

    /**
     * Format date only (no time)
     * 
     * @param string|null $date
     * @return string Format: 31/12/2025
     */
    protected function formatDateOnly($date)
    {
        return $this->formatDate($date, 'd/m/Y');
    }

    /**
     * Format time only (no date)
     * 
     * @param string|null $date
     * @return string Format: 18:29
     */
    protected function formatTimeOnly($date)
    {
        return $this->formatDate($date, 'H:i');
    }

    /**
     * Format for API responses - returns the date in multiple formats
     * 
     * @param string|null $date
     * @return array
     */
    protected function formatDateForApi($date)
    {
        if (!$date) {
            return [
                'raw' => null,
                'formatted' => '-',
                'date_only' => '-',
                'time_only' => '-',
            ];
        }

        try {
            $carbon = Carbon::parse($date);
            return [
                'raw' => $date,
                'formatted' => $carbon->format('d/m/Y H:i'),
                'date_only' => $carbon->format('d/m/Y'),
                'time_only' => $carbon->format('H:i'),
                'relative' => $carbon->diffForHumans(),
            ];
        } catch (\Exception $e) {
            return [
                'raw' => $date,
                'formatted' => '-',
                'date_only' => '-',
                'time_only' => '-',
            ];
        }
    }

    /**
     * Format multiple date fields in an object or array
     * 
     * @param object|array $data
     * @param array $dateFields Fields to format (e.g., ['created_at', 'updated_at'])
     * @param string $format
     * @return object|array
     */
    protected function formatDatesInData($data, array $dateFields = ['created_at', 'updated_at'], $format = 'd/m/Y H:i')
    {
        $isArray = is_array($data);
        $item = $isArray ? $data : (array) $data;

        foreach ($dateFields as $field) {
            if (isset($item[$field])) {
                $item[$field] = $this->formatDate($item[$field], $format);
            }
        }

        return $isArray ? $item : (object) $item;
    }

    /**
     * Format dates in a collection/array of items
     * 
     * @param array|\Illuminate\Support\Collection $items
     * @param array $dateFields
     * @param string $format
     * @return array|\Illuminate\Support\Collection
     */
    protected function formatDatesInCollection($items, array $dateFields = ['created_at', 'updated_at'], $format = 'd/m/Y H:i')
    {
        $isCollection = $items instanceof \Illuminate\Support\Collection;
        $collection = $isCollection ? $items : collect($items);

        $formatted = $collection->map(function ($item) use ($dateFields, $format) {
            return $this->formatDatesInData($item, $dateFields, $format);
        });

        return $isCollection ? $formatted : $formatted->all();
    }
}

