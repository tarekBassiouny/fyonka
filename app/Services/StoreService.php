<?php

namespace App\Services;

use App\Models\Store;
use Illuminate\Support\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Interfaces\StoreServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StoreService implements StoreServiceInterface
{
    public function list(array $data): LengthAwarePaginator
    {
        $query = Store::query();

        if (!empty($data['name'])) {
            $query->where('name', 'like', '%' . $data['name'] . '%');
        }

        return $query
            ->orderBy('id', 'desc')
            ->paginate($data['per_page'] ?? 10)
            ->withQueryString();
    }

    public function create(array $data, UploadedFile $file): Store
    {
        if ($file) {
            $data['image_path'] = $file->store('store_image/' . $data['name'], 'public');
        }

        return Store::create($data);
    }

    public function update(Store $store, array $data, ?UploadedFile $file): bool
    {
        if ($file) {
            if ($store->image_path) {
                Storage::disk('public')->delete($store->image_path);
            }

            $data['image_path'] = $file->store('uploads/' . $store->name, 'public');
        }
        return $store->update($data);
    }

    public function delete(Store $store): ?bool
    {
        if ($store->image_path && Storage::disk('public')->exists($store->image_path)) {
            Storage::disk('public')->delete($store->image_path);
        }
        return $store->delete();
    }

    public function getAllStores(): Collection
    {
        return Store::select('id', 'name', 'image_path')->get();
    }
}
