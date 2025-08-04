<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Video downloader</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>

<main role="main">
    <div class="container">
        <div class="row">
            <div class="col-6 text-center" style="margin: 0 auto;">
                <h1 class="mt-5"><?php echo $__env->yieldContent('title'); ?></h1>
                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </div>
    </div>
</main>

</body>
</html>
<?php /**PATH D:\laragon\www\vemitiendabackend\resources\views/videos/base.blade.php ENDPATH**/ ?>