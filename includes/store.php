<?php
const DATA_DIR = __DIR__ . '/../data/';

const WILDLIFE_CATEGORIES = ['Bird', 'Mammal', 'Reptile', 'Fish', 'Plant', 'Other'];
const CONSERVATION_STATUS = ['Least Concern', 'Near Threatened', 'Vulnerable', 'Endangered', 'Critically Endangered', 'Not Evaluated'];
const BUSINESS_TYPES      = ['Resort', 'Restaurant', 'Tour Operator', 'Tour Guide', 'Souvenir Shop', 'Other'];

function load_data(string $name): array
{
    $file = DATA_DIR . $name . '.json';
    if (!file_exists($file)) {
        return [];
    }
    $rows = json_decode(file_get_contents($file), true);
    return is_array($rows) ? $rows : [];
}

function save_data(string $name, array $rows): void
{
    file_put_contents(
        DATA_DIR . $name . '.json',
        json_encode(array_values($rows), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        LOCK_EX
    );
}

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function peso(float $amount): string
{
    return '₱' . number_format($amount, 2);
}
