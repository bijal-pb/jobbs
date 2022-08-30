<div class="mb-2">
            <a class="flex items-center justify-between px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-blue-500 border border-transparent rounded-lg active:bg-blue-500 hover:bg-blue-500 focus:outline-none focus:shadow-outline-blue float-right cursor-pointer"  wire:click="cancel()">
                <svg class="w-4 h-4 mr-2 -ml-1" fill="currentColor" aria-hidden="true" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clipRule="evenodd" />
                </svg>
                <span>Back</span>
            </a>
        </div>
</br></br>
        <div class="w-full mb-8 overflow-hidden rounded-lg shadow-xs bg-white rounded-lg shadow-md dark:bg-gray-800">
        <table style="margin-right: auto; text-align: -webkit-match-parent; white-space: pre;">
        @if($user_order->userRequest != null)
            <tr>
                <th class="px-20 py-5 text-right">Customer:</th>
                <td class="text-left">{{ $user_order->userRequest->from_user->first_name }}</td>
           </tr> 
       @endif
       @if($user_order->userRequest != null)            
            <tr>
                <th class="px-20 py-5 text-right">Provider:</th>
                <td class="text-left">{{ $user_order->userRequest->to_user->first_name }}</td>
            </tr>
        @endif
            <tr>
                <th class="px-20 py-5 text-right">Order Rate : </th>
                <td class="text-left">0</td>
            </tr>
            <tr>
                <th class="px-20 py-5 text-right">Reach time : </th>
                <td class="text-left">{{ $user_order->reach_time }} </td>
            </tr>
            <tr>
                <th class="px-20 py-5 text-right">Start time: </th>
                <td class="text-left">{{ $user_order->start_time }} </td>
            </tr>
            <tr>
                <th class="px-20 py-5 text-right">Complete time : </th>
                <td class="text-left">{{ $user_order->complete_time }} </td>
            </tr>
            <tr>
               <th class="px-20 py-5 text-right">Price : </th>
               <td class="text-left">{{ $user_order->price }} </td>
            </tr>
            <tr>
               <th class="px-20 py-5 text-right">Service fee : </th>
               <td class="text-left">{{ $user_order->service_fee }} </td>
            </tr>
            <tr>
               <th class="px-20 py-5 text-right">Discount : </th>
               <td class="text-left">{{ $user_order->discount }} </td>
            </tr>
            <tr>
               <th class="px-20 py-5 text-right">Total amount : </th>
               <td class="text-left">{{ $user_order->total_amount }} </td>
            </tr>
            <tr>
               <th class="px-20 py-5 text-right">Status : </th>
               <td class="text-left">{{ $user_order->status }} </td>
            </tr>   
      

        @if($user_order->userRequest != null)
           <div class="w-full mb-8 overflow-hidden rounded-lg shadow-xs bg-white rounded-lg shadow-md dark:bg-gray-800">
                   <tr>
                    <tr>
                        <th class="px-20 py-5 text-right">User Service :</th>
                        <td class="text-left">{{ $user_order->userRequest->user_service->service_category->name }}</td>
                    </tr>
            </table>
        </div>
        @endif
 </div> 