
<x-app-layout title="Service Category">
    <div class="container grid px-6 mx-auto">
        <h6 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Service Category Management
        </h6>  
        <livewire:service-category-datatable exportable/>
    </div>
    <script type="text/javascript">
        function closeAlert(event){
            let element = event.target;
            while(element.nodeName !== "BUTTON"){
                element = element.parentNode;
            }
            element.parentNode.parentNode.removeChild(element.parentNode);
        }
    </script>
    @if(Session::has('message'))
    <script>
        $(function(){
                toastr.success("{{ Session::get('message') }}");
            })
    </script>
    @endif
</x-app-layout>