<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Excel File</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-6 max-w-7xl w-full">

    <div class="w-full max-w-5xl bg-white shadow-lg rounded-xl p-6">
        <div class="flex justify-center mb-4">
            <img src="{{ asset('images/EDVERTICA.png') }}" alt="Upload Image" class="h-16 w-16 object-cover rounded-full">
        </div>
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Upload Excel File</h2>

        {{-- Success and Error Messages --}}
        @if(session('success'))
            <p class="text-green-600 font-semibold bg-green-100 p-2 rounded-md">{{ session('success') }}</p>
        @endif
        @if($errors->any())
            <div class="text-red-600 font-semibold bg-red-100 p-2 rounded-md">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Upload Form --}}
        <form id="uploadForm" action="{{ route('upload.excel') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <label class="block">
                <span class="text-gray-700">Choose an Excel file</span>
                <input type="file" name="file" required 
                    class="block w-full mt-2 p-2 border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
            </label>

            {{-- File Type Selection --}}
            {{-- <div class="mt-4">
                <span class="text-gray-700">Select File Type:</span>
                <div class="space-x-4 mt-2">
                    <label>
                        <input type="radio" name="file_type" value="TADA" class="file-type-radio"> TADA Raw Files
                    </label>
                    <label>
                        <input type="radio" name="file_type" value="RFM" class="file-type-radio"> RFM
                    </label>
                    <label>
                        <input type="radio" name="file_type" value="NPS" class="file-type-radio" id="npsRadio"> NPS
                    </label>
                </div>
            </div> --}}

            {{-- Segment Selection --}}
            <div class="grid grid-cols-3 gap-4 mt-2">
                <span class="text-gray-700">Select Segments:</span>
                <label>
                    <input type="checkbox" name="mfm_segment" value="1" class="segment-checkbox"> MFM Segment
                <label>
                    <input type="checkbox" name="tr_segment" value="1" class="segment-checkbox"> TR Segment
                <label>
                    <input type="checkbox" name="nyss_segment" value="1" class="segment-checkbox"> NYSS Segment
                </label>
                </div>


            {{-- Upload Button --}}
            <div class="flex gap-4 mt-4">
                <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition">
                    Upload
                </button>
            </div>
        </form>

        {{-- Display Data --}}
        @if(isset($data) && count($data) > 0)
            <h3 class="text-xl font-semibold text-gray-800 mt-6">Stored Excel Data</h3>

            {{-- Download Buttons --}}
            <div class="flex justify-center gap-8 mt-6">
                <a href="{{ route('download.excel') }}" 
                class="flex items-center gap-2 justify-center font-semibold py-3 px-6 rounded-lg shadow-lg transition-transform transform hover:-translate-y-1 hover:scale-105">
                📊 Download Excel
                </a>
            
                <a href="{{ route('download.pdf') }}" 
                class="flex items-center gap-2 justify-center font-semibold py-3 px-6 rounded-lg shadow-lg transition-transform transform hover:-translate-y-1 hover:scale-105">
                📄 Download PDF
                </a>
            </div>

            {{-- Table Display --}}
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
                            <th class="p-3">Last Transaction Date</th>
                            <th class="p-3">Last Visited Store</th>
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
                                <td class="p-3">{{ $row->mfm_segment ? '✔' : '✖' }}</td>
                                <td class="p-3">{{ $row->tr_segment ? '✔' : '✖' }}</td>
                                <td class="p-3">{{ $row->nyss_segment ? '✔' : '✖' }}</td>
                                <td class="p-3">{{ $row->last_transaction_date }}</td>
                                <td class="p-3">{{ $row->last_visited_store }}</td>
                                <td class="p-3">{{ $row->remaining_points }}</td>
                                <td class="p-3">{{ $row->points_last_updated }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            <div class="mt-4">
                {{ $data->appends(['per_page' => request('per_page', 10)])->links() }}
            </div>
        @endif
    </div>

    {{-- JavaScript to disable/enable segment checkboxes based on radio selection --}}
    {{-- <script>
        document.querySelectorAll('.file-type-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                let isNPS = this.value === 'NPS';
                document.querySelectorAll('.segment-checkbox').forEach(checkbox => {
                    checkbox.checked = false;
                    checkbox.disabled = isNPS;
                });
            });
        });
    </script> --}}


</body>
</html>
