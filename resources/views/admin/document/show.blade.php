        <div class="mb-2">
            <a class="flex items-center justify-between px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-blue-500 border border-transparent rounded-lg active:bg-blue-500 hover:bg-blue-500 focus:outline-none focus:shadow-outline-blue float-right cursor-pointer"  wire:click="cancel()">
                <svg class="w-4 h-4 mr-2 -ml-1" fill="currentColor" aria-hidden="true" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clipRule="evenodd" />
                </svg>
                <span>Back</span>
            </a>
        </div>
    </br>
    </br>
    
        <div class="w-full mb-8 overflow-hidden rounded-lg shadow-xs bg-white rounded-lg shadow-md dark:bg-gray-800">
        <table style="margin-right: auto; text-align: -webkit-match-parent; white-space: pre;">
            <tr>
                <th class="px-20 py-5 text-left">Document Image: </th>
                <td class="text-left">
                @if($user_doc->document != null) <img src="{{ $user_doc->document }}" width="100" height="100" enctype="multipart/form-data"/>@else Not uploaded @endif
                </td>
            </tr>
            <tr>
                <th class="px-20 py-5 text-left">Status: </th>
                <td class="text-left">@if($user_doc->status == 0)require action @elseif($user_doc->status == 1)Approved @else Disapproved @endif </td>
            </tr>

             </table>
            </div>
           @if($user_doc->uploadby != null)
           <div class="w-full mb-8 overflow-hidden rounded-lg shadow-xs bg-white rounded-lg shadow-md dark:bg-gray-800">
            <table style="margin-right: auto; text-align: -webkit-match-parent; white-space: pre;">
                <thead>
                    <tr>
                    <th class="px-20 py-5 text-left">Upload by : </th>
                    </tr>
                    <tr>
                        <th class="px-20 py-5 text-left">Name :</th>
                        <td class="text-left">{{ $user_doc->uploadby->first_name }} {{ $user_doc->uploadby->last_name }}</td>
                    </tr>

                </thead>
        </table>
    </div>
  @endif
  @if($user_doc->documentname != null)
           <div class="w-full mb-8 overflow-hidden rounded-lg shadow-xs bg-white rounded-lg shadow-md dark:bg-gray-800">
            <table style="margin-right: auto; text-align: -webkit-match-parent; white-space: pre;">
                <thead>
                    <tr>
                    <th class="px-20 py-5 text-left">Document name :</th>
                    </tr>
                    <tr>
                        <th class="px-20 py-5 text-left">Name :</th>
                        <td class="text-left">{{ $user_doc->documentname->name }}</td>
                    </tr>

                </thead>
        </table>
    </div>
  @endif

       
 