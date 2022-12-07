@extends('layouts.backend')

@section('content')
<style>
.passed{ color:red; }
</style>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Business List</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ ('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">Business List</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">

          <div class="card">
              <div class="card-header">Business List</h3>
                <button type="button" class="btn btn-success btn-sm float-right" data-toggle="modal" data-target="#createModal">
                  Add New
                </button>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="datatable" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>ID</th>
                    <th>Business Name</th>
                    <th>Brand Name</th>
                    <th>Time Interval</th>
                    <th>Logo</th>
                    <th>Banner</th>
                    <th>Business Email</th>
                    <th>Languages</th>
                    <th>Access Modules</th>
                    <th>Remember Token</th>
                    <th>Is Active</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($businesses as $key => $value)
                        <tr>
                            <td class='id'>{{ $value->id }}</td>
                            <td class='businessName'>{{ $value->business_name }}</td>
                            <td class='brandName'>{{ $value->brand_name }}</td>
                            <td class='timeInteval'>{{ $value->time_interval }}</td>
                            <td class='logo'>{{ $value->logo }}</td>
                            <td class='banner'>{{ $value->banner }}</td>
                            <td class='email'>{{ $value->business_email }}</td>
                            <td class='language'>{{ $value->languages }}</td>
                            <td class='accessModule'>{{ $value->access_modules }}</td>
                            <td class='rememberToken'>{{ $value->remember_token }}</td>
                            <td class='active'>{{ $value->is_active }}</td>
                            <td class='create'>{{ $value->created_at }}</td>
                            <td class='update'>{{ $value->updated_at }}</td>                            
                            <td>
                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#editModal" onclick="copyData(this)">
                                  Edit
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal"  onclick="copyId(this)">
                                  Delete
                                </button>
                            </td>                            
                        </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
            </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>


<!-- Modal For Add New Entry -->
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createModalLabel">Add Data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form onsubmit="addNewData(this)" name="createForm" id="createForm">
          @csrf
          <div class="form-group">
            <input type="text" name="business_name" class="form-control" placeholder="Business Name" required>
          </div>
          <div class="form-group">
            <input type="text" name="brand_name" class="form-control" placeholder="Brand Name" required>
          </div>
          <div class="form-group">
            <input  type="number" min="0" name="time_interval" class="form-control" placeholder="Time Interval" required>
          </div>
          <div class="form-group">
            <input type="text" name="logo" class="form-control" placeholder="Logo" required>
          </div>
          <div class="form-group">
            <input type="text" name="banner" class="form-control" placeholder="Banner" required>
          </div>
          <div class="form-group">
            <input type="text" name="business_email" class="form-control" placeholder="Business Email" required>
          </div>
          <div class="form-group">
            <input type="number" min="0" name="is_active" class="form-control" placeholder="Is Active" required>
          </div>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Add</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal For Edit Entry -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Edit Data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form onsubmit="editData(this)" name="editForm" id="editForm">
            @csrf
            <input type="hidden" name="id" class="id">
            <div class="form-group">
              <label>Business Name</label>
              <input type="text" name="business_name" class="form-control business_name" placeholder="Business Name" required>
            </div>
            <div class="form-group">
              <label>Brand Name</label>
              <input type="text" name="brand_name" class="form-control brand_name" placeholder="Brand Name" required>
            </div>
            <div class="form-group">
              <label>Time Interval</label>
              <input  type="number" min="0" name="time_interval" class="form-control time_interval" placeholder="Time Interval" required>
            </div>
            <div class="form-group">
              <label>Business logo</label>
              <input type="text" name="logo" class="form-control logo" placeholder="Logo" required>
            </div>
            <div class="form-group">
              <label>Business Banner</label>
              <input type="text" name="banner" class="form-control banner" placeholder="Banner" required>
            </div>
            <div class="form-group">
              <label>Business email</label>
              <input type="text" name="business_email" class="form-control business_email" placeholder="Business Email" required>
            </div>
            <div class="form-group">
              <label>Is Active</label>
              <input type="number" min="0" name="is_active" class="form-control is_active" placeholder="Is Active" required>
            </div>
          
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Update</button>
      </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal For delete Entry -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Delete Data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <h5>Are you sure you want to delete? You can't undo this once deleted!</h5>
        <form name="deleteForm" id="deleteForm" onsubmit="deleteData(this)">
          @csrf
            <input type="hidden" name="id" class="id">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-danger">Yes</button>
      </form>
      </div>
    </div>
  </div>
</div>

@stop

@section('scripts')
<script>

  function addNewData(obj){
    event.preventDefault();
    
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000
    });

    $.ajax({
      type: 'POST',
      url: '/database-business-add',
      data: jQuery(obj).serialize(),
      dataType: 'json',
      success: function (data) {
        $("#createModal").modal('hide');
        if(data == 'success'){
          Toast.fire({
              icon: 'success',
              title: 'Data has been added!'
          });
        }
        else if(data == 'exist'){
          Toast.fire({
              icon: 'error',
              title: 'Data already exist with unique column.'
          })
        }
        else{
          Toast.fire({
              icon: 'error',
              title: 'There is an error to add data.'
          })
        }
      },
      error: function (data) {
        $("#createModal").modal('hide');
        Toast.fire({
              icon: 'error',
              title: 'There is an error to add data.'
          })
      }
    });
  }

  function editData(obj){

    event.preventDefault();
    
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000
    });

    $.ajax({
      type: 'POST',
      url: '/database-business-edit',
      data: jQuery(obj).serialize(),
      dataType: 'json',
      success: function (data) {
        $("#editModal").modal('hide');
        if(data == 'success'){
          Toast.fire({
              icon: 'success',
              title: 'Data has been updated!'
          });
        }
        else if(data == 'exist'){
          Toast.fire({
              icon: 'error',
              title: 'Data already exist with unique column.'
          })
        }
        else{
          Toast.fire({
              icon: 'error',
              title: 'There is an error to update data.'
          })
        }
      },
      error: function (data) {
        $("#editModal").modal('hide');
        Toast.fire({
              icon: 'error',
              title: 'There is an error to update data.'
          })
      }
    });
  }

function deleteData(obj){

  event.preventDefault();

  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
  });

  $.ajax({
    type: 'POST',
    url: '/database-business-delete',
    data: jQuery(obj).serialize(),
    dataType: 'json',
    success: function (data) {
      $("#deleteModal").modal('hide');
      if(data == 'success'){
        Toast.fire({
            icon: 'success',
            title: 'Data has been deleted!'
        });
      }
      else{
        Toast.fire({
            icon: 'error',
            title: 'There is an error to update data.'
        })
      }
    },
    error: function (data) {
      $("#deleteModal").modal('hide');
      Toast.fire({
            icon: 'error',
            title: 'There is an error to delete data.'
        })
    }
  });
}

function copyData(obj){
  if(jQuery(obj).parentsUntil('tr').hasClass('child'))
    var tr = jQuery('tr.parent');
  else  
    var tr = jQuery(obj).closest('tr');

    jQuery('#editForm .id').val(jQuery(tr).find('.id').text());
    jQuery('#editForm .business_name').val(jQuery(tr).find('.businessName').text());
    jQuery('#editForm .id').val(jQuery(tr).find('.id').text());
    jQuery('#editForm .brand_name').val(jQuery(tr).find('.brandName').text());
    jQuery('#editForm .time_interval').val(jQuery(tr).find('.timeInteval').text());
    jQuery('#editForm .logo').val(jQuery(tr).find('.logo').text());
    jQuery('#editForm .banner').val(jQuery(tr).find('.banner').text());
    jQuery('#editForm .business_email').val(jQuery(tr).find('.email').text());
    jQuery('#editForm .is_active').val(jQuery(tr).find('.active').text());

}

function copyId(obj){
  if(jQuery(obj).parentsUntil('tr').hasClass('child'))
    var tr = jQuery('tr.parent');
  else  
    var tr = jQuery(obj).closest('tr');

    jQuery('#deleteForm .id').val(jQuery(tr).find('.id').text());

}




</script>
@stop