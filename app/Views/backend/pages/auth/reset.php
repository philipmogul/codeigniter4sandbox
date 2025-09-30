<?= $this->extend("backend/layouts/auth-layout") ?>
<?= $this->section("content") ?>

<div class="login-box bg-white box-shadow border-radius-10">
    <div class="login-title">
        <h2 class="text-center text-primary">Reset Password</h2>
    </div>
    <h6 class="mb-20">Enter your new password, confirm and submit</h6>


    <?php $validation = \Config\Services::validation(); ?>
    <form action="<?= route_to('reset-password-handler', $token); ?>" method="POST">
        <?= csrf_field() ?>

        <?php if(!empty(session()->getFlashdata('success'))): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success'); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>


        <?php if(!empty(session()->getFlashdata('fail'))): ?>
            <div class="alert alert-danger">
                <?= session()->getFlashdata('fail'); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="input-group custom">
            <input type="password" class="form-control form-control-lg" name="new_password" value="<?= set_value('new_password'); ?>" placeholder="New Password">
            <div class="input-group-append custom">
                <span class="input-group-text"><i class="dw dw-padlock1"></i></span>
            </div>
        </div>

            <?php if( $validation->getError('new_password') ): ?>
                <div class="alert alert-danger mt-2">
                    <?= $validation->getError('new_password'); ?>
                </div>
            <?php endif; ?>

        <div class="input-group custom">
            <input type="password" class="form-control form-control-lg" name="confirm_new_password" value="<?= set_value('confirm_new_password') ?>" placeholder="Confirm New Password">
            <div class="input-group-append custom">
                <span class="input-group-text"><i class="dw dw-padlock1"></i></span>
            </div>
        </div>

                <?php if( $validation->getError('confirm_new_password') ): ?>
                <div class="alert alert-danger mt-2">
                    <?= $validation->getError('confirm_new_password'); ?>
                </div>
            <?php endif; ?>

        <div class="row align-items-center">
            <div class="col-5">
                <div class="input-group mb-0">
                <input class="btn btn-primary btn-lg btn-block" type="submit" value="Submit">
										
                </div>
            </div>
        </div>
    </form>
</div>


<?= $this->endSection() ?>