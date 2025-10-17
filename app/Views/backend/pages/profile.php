<?= $this->extend('backend/layouts/pages-layout') ?>
<?= $this->section('content') ?>

<div class="main-container">
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="title">
                            <h4>Profile</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="<?= route_to('admin.home'); ?>">Home</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Profile
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-30">
                    <div class="pd-20 card-box height-100-p">
                        <div class="profile-photo">
                            <a href="modal" class="edit-avatar"><i class="fa fa-pencil"></i></a>
                            <img src="<?= get_user()->picture == null ? '/images/users/ls.jpg' : '/images/users/'.get_user()->picture(); ?>" alt="" class="avatar-photo ci-avatar-photo">
                           
                        </div>
                        <h5 class="text-center h5 mb-0 ci-user-name"><?= get_user()->username; ?></h5>
                        <p class="text-center text-muted font-14 ci-user-email">
                            <?= get_user()->email; ?>
                        </p>
                    </div>
                </div>
                <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 mb-30">
                    <div class="card-box height-100-p overflow-hidden">
                        <div class="profile-tab height-100-p">
                            <div class="tab height-100-p">
                                <ul class="nav nav-tabs customtab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#personal_details" role="tab">Personal Details</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#change_password" role="tab">Change Password</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <!-- Timeline Tab start -->
                                    <div class="tab-pane fade show active" id="personal_details" role="tabpanel">
                                        <div class="pd-20">
                                            <form action="<?= route_to('update-personal-details'); ?>" method="POST" id="personal_details_from">
                                                <?= csrf_field(); ?>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="Name">Name</label>
                                                            <input class="form-control" type="text" name="name" placeholder="Enter Full Name" value="<?= get_user()->name; ?>" />
                                                            <span class="text-danger error-text name_error"></span>
                                                        </div>
                                                    </div> 

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="Username">Username</label>
                                                            <input class="form-control" type="text" name="username" placeholder="Enter Username" value="<?= get_user()->username; ?>" />
                                                            <span class="text-danger error-text username_error"></span>
                                                        </div>
                                                    </div> 
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="Bio">Bio</label>
                                                        <textarea class="form-control" name="bio" id="" rows="10" cols="30" placeholder="Enter Bio ..."><?= get_user()->bio; ?></textarea>
                                                        <span class="text-danger error-text bio_error"></span>
                                                    </div>

                                                    <div class="form-group">
                                                        <button type="submit" class="btn btn-primary" id="update_profile_btn">
                                                            Update Profile
                                                        </button>
                                                    </div>

                                                



                                            </form>
                                        </div>
                                    </div>
                                    <!-- Timeline Tab End -->
                                    <!-- Tasks Tab start -->
                                    <div class="tab-pane fade" id="change_password" role="tabpanel">
                                        <div class="pd-20 profile-task-wrap">
                                            === CHANGE PASSWORD ===
                                        </div>
                                    </div>
                                    <!-- Tasks Tab End -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
$('#personal_details_from').on('submit',function(e)
{
    e.preventDefault();
    let form = this;
    var formData = new FormData(form);

    $.ajax(
        {
            url: $(form).attr('action'),
            method: $(form).attr('method'),
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function()
            {
                // $(form).find('span.error-text').text('');
                // $('#update_profile_btn').prop('disabled', true);
                // $('#update_profile_btn').html('Updating...');
                toastr.remove();
                $(form).find('span.error-text').text('');
            },
            success: function(response)
            {
                if( $.isEmptyObject(response.error) )
                {
                    if (response.status == 1) {
                        if (response.user_info && response.user_info.name) {
                            $('.ci-user-name').html(response.user_info.name);
                        }
                        toastr.success(response.msg);
                    } else if (response.error) {
                        // Display validation errors
                        for (const key in response.error) {
                            toastr.error(response.error[key]);
                        }
                    } else {
                        toastr.error(response.msg);
                    }
                }
                else
                {
                    $.each(response.error, function(prefix, val){
                        $(form).find('span.'+prefix+'_error').text(val);
                    });
                }
            },
            complete: function()
            {
                $('#update_profile_btn').prop('disabled', false);
                $('#update_profile_btn').html('Update Profile');
            }
        }
    );

});
</script>
<?= $this->endSection(); ?>