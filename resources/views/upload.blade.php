<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Excel File</title>
    @vite('resources/css/app.css')
</head>
<body class="flex items-center justify-center min-h-screen p-6">

    <div class="w-full max-w-5xl bg-gray-200 shadow-lg rounded-xl p-6">
        <div class="flex justify-center mb-4">
            <img src="{{ asset('images/EDVERTICA.png') }}" alt="Upload Image" class="h-1 w-16 object-cover rounded-full">
        </div>
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Upload Excel File</h2>

        @if(session('success'))
            <p class="text-green-600 font-semibold">{{ session('success') }}</p>
        @endif
        @if($errors->any())
        
            <p class="text-red-600 font-semibold">{{ implode('', $errors->all(':message')) }}</p>
        @endif

        <form action="{{ route('upload.excel') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <label class="block">
                <span class="text-gray-700">Choose an Excel file</span>
                <input type="file" name="file" required 
                    class="block w-full mt-2 p-2 border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
            </label>
        
            <br>
            <label>
                <input type="checkbox" name="mfm_segment" value="1">  MFM Segment
            </label>
            
            <label>
                <input type="checkbox" name="tr_segment" value="1">  TR Segment
            </label>
            
            <label>
                <input type="checkbox" name="nyss_segment" value="1">  NYSS Segment
            </label>
            
            <!-- Add Radio Buttons for TADA, RFM, and NPS -->
            <div class="mt-4">
                <label class="block">
                    <span class="text-gray-700">Select File Type:</span>
                    <div class="space-x-4 mt-2">
                        <label>
                            <input type="radio" name="file_type" value="TADA" class="mr-2"> TADA Raw Files
                        </label>
                        <label>
                            <input type="radio" name="file_type" value="RFM" class="mr-2"> RFM
                        </label>
                        <label>
                            <input type="radio" name="file_type" value="NPS" class="mr-2"> NPS
                        </label>
                    </div>
                </label>
            </div>
        
            <br>
            <button type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition">
                Upload
            </button>
        </form>
        

        @if(isset($data) && count($data) > 0)
            <h3 class="text-xl font-semibold text-gray-800 mt-6">Stored Excel Data</h3>
            <div class="flex justify-center gap-8 mt-6">
                <!-- Download Excel Button -->
                <a href="{{ route('download.excel') }}" 
                   class="flex items-center gap-2 justify-center  font-semibold py-3 px-6 rounded-lg shadow-lg transition-transform transform hover:-translate-y-1 hover:scale-105">
                    📊 Download Excel
                </a>
            
                <!-- Download PDF Button -->
                <a href="{{ route('download.pdf') }}" 
                   class="flex items-center gap-2 justify-center font-semibold py-3 px-6 rounded-lg shadow-lg transition-transform transform hover:-translate-y-1 hover:scale-105">
                    📄 Download PDF
                </a>
            </div>
            
            <div class="overflow-x-auto mt-4">
                <table class="w-full border-collapse bg-white shadow-md rounded-lg">
                    <thead class="bg-gray-200 text-gray-700">
                        <tr>
                            <th class="p-3">ID</th>
                            <th class="p-3">Card No</th>
                            <th class="p-3">Email</th>
                            <th class="p-3">Last Name</th>
                            <th class="p-3">Phone No</th>
                            <th class="p-3">Brand</th>
                            <th class="p-3">MFM Segment</th>
                            <th class="p-3">TR Segment</th>
                            <th class="p-3">NYSS Segment</th>
                            <th class="p-3">Last Transaction</th>
                            <th class="p-3">Last Store</th>
                            <th class="p-3">Remaining Points</th>
                            <th class="p-3">Points Last Updated</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($data as $row)
                            <tr class="hover:bg-gray-100 transition">
                                <td class="p-3 text-center">{{ $row->id }}</td>
                                <td class="p-3">{{ $row->card_no }}</td>
                                <td class="p-3">{{ $row->email }}</td>
                                <td class="p-3">{{ $row->last_name }}</td>
                                <td class="p-3">{{ $row->phone_no }}</td>
                                <td class="p-3">{{ $row->brand }}</td>
                                <td class="p-3">{{ $row->mfm_segment }}</td>
                                <td class="p-3">{{ $row->tr_segment }}</td>
                                <td class="p-3">{{ $row->nyss_segment }}</td>
                                <td class="p-3">{{ $row->last_transaction_date }}</td>
                                <td class="p-3">{{ $row->last_visited_store }}</td>
                                <td class="p-3">{{ $row->remaining_points }}</td>
                                <td class="p-3">{{ $row->points_last_updated }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    
                    <!-- Pagination Links -->
                    <div class="mt-4">
                        {{ $data->appends(['per_page' => request('per_page', 5)])->links() }}
                    </div>                     
                </table>
            </div>
        @endif
    </div>

</body>
</html>
