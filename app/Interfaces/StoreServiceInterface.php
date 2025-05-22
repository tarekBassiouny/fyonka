<?php

namespace App\Interfaces;

use App\Models\Store;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface StoreServiceInterface
{
    public function list(array $data): LengthAwarePaginator;
    public function create(array $data, UploadedFile $file): Store;
    public function update(Store $store, array $data, ?UploadedFile $file): bool;
    public function delete(Store $store): ?bool;
    public function getAllStores(): Collection;
}
