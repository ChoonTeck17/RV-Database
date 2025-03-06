<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload RFM Excel File</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-6 max-w-7xl w-full">
    <div class="w-full max-w-5xl bg-white shadow-lg rounded-xl p-6">
        <div class="flex justify-center mb-4">
            <img src="{{ asset('images/EDVERTICA.png') }}" alt="Upload Image" class="h-16 w-16 object-cover rounded-full">
        </div>
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Upload RFM Excel File</h2>

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
        <form id="uploadForm" action="{{ route('rfm.upload.process') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <label class="block">
                <span class="text-gray-700">Choose an RFM Excel file</span>
                <input type="file" name="file" required 
                    class="block w-full mt-2 p-2 border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
            </label>

            <div class="flex gap-4 mt-4">
                <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition">
                    Upload
                </button>
            </div>
        </form>

        {{-- Display Data --}}
        @if(isset($data) && $data->count() > 0)
            <h3 class="text-xl font-semibold text-gray-800 mt-6">Stored RFM Data</h3>

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

            <div class="overflow-x-auto mt-4">
                <table class="w-full border-collapse bg-white shadow-md rounded-lg border border-gray-300">
                    <thead class="bg-gray-200 text-gray-700">
                        <tr>
                            <th class="p-3 border border-gray-300 text-center">ID</th>
                            <th class="p-3 border border-gray-300 text-center">Card No</th>
                            <th class="p-3 border border-gray-300 text-center">Email</th>
                            <th class="p-3 border border-gray-300 text-center">Last Name</th>
                            <th class="p-3 border border-gray-300 text-center">Phone No</th>
                            <th class="p-3 border border-gray-300 text-center">Brand</th>
                            <th class="p-3 border border-gray-300 text-center">MFM Segment</th>
                            <th class="p-3 border border-gray-300 text-center">TR Segment</th>
                            <th class="p-3 border border-gray-300 text-center">NYSS Segment</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($data as $row)
                            <tr class="hover:bg-gray-100 transition">
                                <td class="p-3 border border-gray-300 text-center">{{ $row->id }}</td>
                                <td class="p-3 border border-gray-300 text-center">{{ $row->card_no }}</td>
                                <td class="p-3 border border-gray-300 text-center">{{ $row->email }}</td>
                                <td class="p-3 border border-gray-300 text-center">{{ $row->last_name }}</td>
                                <td class="p-3 border border-gray-300 text-center">{{ $row->phone_no }}</td>
                                <td class="p-3 border border-gray-300 text-center">{{ $row->brand }}</td>
                                <td class="p-3 border border-gray-300 text-center">{{ $row->mfm_segment }}</td>
                                <td class="p-3 border border-gray-300 text-center">{{ $row->tr_segment }}</td>
                                <td class="p-3 border border-gray-300 text-center">{{ $row->nyss_segment }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $data->links() }}
            </div>
        @endif
    </div>
</body>
</html>