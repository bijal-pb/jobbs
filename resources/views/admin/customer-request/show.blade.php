        <div class="mb-2">
            <a class="flex items-center justify-between px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-blue-500 border border-transparent rounded-lg active:bg-blue-500 hover:bg-blue-500 focus:outline-none focus:shadow-outline-blue float-right cursor-pointer"  wire:click="cancel()">
                <svg class="w-4 h-4 mr-2 -ml-1" fill="currentColor" aria-hidden="true" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clipRule="evenodd" />
                </svg>
                <span>Back</span>
            </a>
        </div>
        <div class="w-full mb-8 overflow-hidden rounded-lg shadow-xs bg-white rounded-lg shadow-md dark:bg-gray-800">
        <table style="margin-right: auto; text-align: -webkit-match-parent; white-space: pre;">
            <tr>
                <th class="px-20 py-5 text-right">Start date : </th>
                <td class="text-left">{{ $customer_request->start_date }} </td>
            </tr>
            <tr>
                <th class="px-20 py-5 text-right">End date : </th>
                <td class="text-left">{{ $customer_request->end_date }} </td>
            </tr>
            <tr>
                <th class="px-20 py-5 text-right">Start time: </th>
                <td class="text-left">{{ $customer_request->start_time }} </td>
            </tr>
            <tr>
                <th class="px-20 py-5 text-right">End time : </th>
                <td class="text-left">{{ $customer_request->end_time }} </td>
            </tr>
            <tr>
            <th class="px-20 py-5 text-right">Address : </th>
                <td><textarea>{{ $customer_request->address }}</textarea> </td>
            </tr>
            <tr>
                <th class="px-20 py-5 text-right">Latitude: </th>
                <td class="text-left">{{ $customer_request->lat }} </td>
            </tr>
            <tr>
                <th class="px-20 py-5 text-right">Longitude : </th>
                <td class="text-left">{{ $customer_request->lang }} </td>
            </tr>
            <tr>
                <th class="px-20 py-5 text-right">status : </th>
                <td class="text-left">{{ $customer_request->status }} </td>
            </tr>
           @if($customer_request->from_user != null)
                 <tr>
                        <th class="px-20 py-5 text-right">Customer :</th>
                        <td class="text-left">{{ $customer_request->from_user->first_name }}</td>
                    </tr>
                    @endif
                </br>
            @if($customer_request->to_user != null)
                    <tr>
                        <th class="px-20 py-5 text-right">Provider:</th>
                        <td class="text-left">{{ $customer_request->to_user->first_name }}</td>
                    </tr>
                     @endif
                </br>
                @if($customer_request->user_service != null)
                    <tr>
                        <th class="px-20 py-5 text-right">User service:</th>
                        <td class="text-left">{{ $customer_request->user_service->id }}</td>
                    </tr>
                @endif
        </table>
 </div> 
