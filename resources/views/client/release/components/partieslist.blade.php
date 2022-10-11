 <div class="panel panel-default">
    <div class="panel-heading">
        Job's Customers (order by)
    </div>
     <table class="table">
         <thead>
             <tr>

                 <th>
                     Contact
                 </th>
                     
             </tr>
                 
         </thead>
         <tbody>
             @foreach($parties as $jobparty)
             <tr>
         

                 <td>
                     @include('client.release.components.contacticon')
                 </td>
             </tr>
             @endforeach
         </tbody>
             
     </table>
</div>