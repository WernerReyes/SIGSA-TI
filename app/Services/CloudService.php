<?php

namespace App\Services;

use App\DTOs\Service\StoreServiceDto;
use App\Models\Service;

class CloudService
{
    public function getAll()
    {
        return Service::orderBy('created_at', 'desc')->get();
    }

    public function store(StoreServiceDto $dto)
    {
        try {
            $service = Service::create([
                'name' => $dto->name,
                'description' => $dto->description,
                'url' => $dto->url,
                'username' => $dto->username,
                'password' => $dto->password,
                'is_active' => $dto->is_active,
            ]);
            return $service;
        } catch (\Exception $e) {
            throw new \Exception('Error creating service: ' . $e->getMessage());
        }
    }

    public function update(Service $service, StoreServiceDto $dto)
    {
        try {
            $service->update([
                'name' => $dto->name,
                'description' => $dto->description,
                'url' => $dto->url,
                'username' => $dto->username,
                'password' => $dto->password,
                'is_active' => $dto->is_active,
            ]);
            return $service;
        } catch (\Exception $e) {
            throw new \Exception('Error updating service: ' . $e->getMessage());
        }
    }

    public function delete(Service $service)
    {
        try {
            $service->delete();
        } catch (\Exception $e) {
            throw new \Exception('Error deleting service: ' . $e->getMessage());
        }
    }



}