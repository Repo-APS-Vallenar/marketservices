<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = ServiceProvider::query();

        if ($request->has('category_id')) {
            $query->whereHas('services', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        if ($request->has('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('company_name', 'like', '%' . $keyword . '%')
                  ->orWhere('bio', 'like', '%' . $keyword . '%')
                  ->orWhereHas('services', function ($q2) use ($keyword) {
                      $q2->where('name', 'like', '%' . $keyword . '%')
                         ->orWhere('description', 'like', '%' . $keyword . '%');
                  });
            });
        }

        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->has('sort_by_rating') && $request->sort_by_rating === 'true') {
            $query->orderBy('rating', 'desc');
        }

        $serviceProviders = $query->with(['user', 'services.category'])->paginate(10);

        return response()->json($serviceProviders);
    }
} 