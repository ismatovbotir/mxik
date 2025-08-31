<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Cards</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body >


<div class="container py-4">

    {{-- 🔢 Top Dashboard Badges --}}
    <div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
        <div class="col">
            <div class="card text-red bg-primary h-100 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="card-title mb-1">Total Products</h6>
                    <h4 class="mb-0">{{ $data['items_count'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-red bg-success h-100 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="card-title mb-1">Unique Countries</h6>
                    <h4 class="mb-0">{{ $data['groups_count'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-red bg-warning h-100 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="card-title mb-1">GTIN Count</h6>
                    <h4 class="mb-0">{{ $data['gtin_count'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-red bg-danger h-100 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="card-title mb-1">Asl Belgi</h6>
                    <h4 class="mb-0">{{ $data['asl_count'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- 🌍 Country Product Counts --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <strong>🌍 Product Count by Country</strong>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Country</th>
                                    <th class="text-end">Product Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data['productsByCountry']['data'] as $item)
                                    <tr>
                                        <td>{{ $item['nameEn'] ?? 'Unknown' }} </td>
                                        <td class="text-end">{{ $item['total'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center">No country data found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 📋 GTIN + Group Product Table 
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <strong>📋 Product Count by GTIN and Group</strong>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle text-center mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>GTIN Name</th>
                                    <th>Group Name</th>
                                    <th>Total Products</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($productsByGtinAndGroup as $index => $item)
                                    <tr>
                                        <td>{{ $productsByGtinAndGroup->firstItem() + $index }}</td>
                                        <td>{{ $item->gtin_name }}</td>
                                        <td>{{ $item->group_name }}</td>
                                        <td>{{ $item->total }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No data found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                
                    <div class="d-flex justify-content-center mt-3">
                        {{ $productsByGtinAndGroup->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    --}}
    {{-- 📎 Footer --}}
    <footer class="mt-5 text-center text-muted small">
        <hr>
        <p>&copy; {{ now()->year }} YourCompany. All rights reserved.</p>
    </footer>

</div>



</body>
</html>