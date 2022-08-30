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
            <tr>
                <th class="px-20 py-5 text-right">Rate: </th>
                <td class="text-left">{{ $user_feedback->rate }} </td>
            </tr>
            <tr>
                <th class="px-20 py-5 text-right">Name : </th>
                <td class="text-left">{{ $user_feedback->name }} </td>
            </tr>
            <tr>
                <th class="px-20 py-5 text-right">Email: </th>
                <td class="text-left">{{ $user_feedback->email }} </td>
            </tr>
            <tr>
                <th class="px-20 py-5 text-right">Feedback : </th>
                <td class="text-left"><textarea class="mt-7">{{ $user_feedback->feedback }}</textarea></td>
            </tr>
            <tr>
            <th class="px-20 py-5 text-right">Suggestion : </th>
                <td class="text-left"><textarea class="mt-7">{{ $user_feedback->suggestion }}</textarea> </td>
            </tr>
        </table>
 </div> 