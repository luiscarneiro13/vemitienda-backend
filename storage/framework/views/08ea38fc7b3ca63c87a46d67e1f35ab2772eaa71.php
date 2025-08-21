<?php $__env->startSection('title', 'Video Downloader'); ?>

<?php $__env->startSection('content'); ?>
    <form method="post" action="<?php echo e(route('prepare')); ?>">
        <?php echo csrf_field(); ?>

        <?php if(Session::has('error')): ?>
            <div class="alert alert-danger"><?php echo e(Session::get('error')); ?></div>
        <?php endif; ?>

        <div class="form-group">
            <input name="url" type="text" required class="form-control <?php $__errorArgs = ['url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="url"
                aria-describedby="url" value="<?php echo e(old('url')); ?>" autocomplete="off" autofocus>

            <?php $__errorArgs = ['url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="form-group">
            <label for="format">Formato de descarga:</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="format" id="mp4" value="mp4" checked>
                <label class="form-check-label" for="mp4">MP4</label>
            </div>

            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="format" id="wav" value="wav">
                <label class="form-check-label" for="wav">WAV</label>
            </div>

            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="format" id="mp3" value="mp3">
                <label class="form-check-label" for="mp3">MP3</label>
            </div>
        </div>

        <div class="text-center">
            <button class="btn btn-lg btn-primary">Descargar</button>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('videos.base', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\vemitiendabackend\resources\views/videos/index.blade.php ENDPATH**/ ?>