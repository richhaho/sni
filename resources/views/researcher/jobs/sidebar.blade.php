<div id="mySidenav" class="sidenav">
  <a href="javascript:void(0)" class="closebtn x" onclick="closeNav()">&times;</a>
  <div>

          <div class="col-xs-12">
          <div class="panel panel-default">
              <div class='panel-heading'>
                  <div class='panel-title'>Search for a Job to copy data from:</div>
              </div>
              <div class="panel-body">
                  
     
                    <select id="copy_job_id" name="copy_job_id" class="form-control" style="width: 100%">
                         @if(strlen(old("copy_job_id")) > 0 )
                         <option value="{{old("copy_job_id")}}">{{ Job::find(old("copy_job_id"))->name}}</option>
                         @else
                         <option value=""></option>
                         @endif
                    </select>

               
              </div>
          </div>
          </div>
       {{ Form::open(array('route' => ['jobs.docopy',$job->id])) }}
     
        <div class="col-xs-12 copy_data">
            
                
            
         
        </div>
          </form>
  </div>
</div>