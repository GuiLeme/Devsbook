<?=$render('header', ['loggedUser' => $loggedUser]);?>
<?php $activeMenu = 'none'?>
<section class="container main">
    <?php if($user->id === $loggedUser->id):?>
        <?php $activeMenu = 'photos';?>
    <?php endif;?>
    <?=$render('sidebar', ['activeMenu' => $activeMenu]);?>
    

    <section class="feed">

        <?=$render('profile-header', ['loggedUser' => $loggedUser, 'user'=>$user, 'isFollowing'=>$isFollowing]);?>
        
        <div class="row">
            <div class="column">
                    
                <div class="box">
                    <div class="box-body">

                        <div class="full-user-photos">
                            
                            <?php if(count($user->photos) === 0):?>
                                <div class="no-photos">Este usuário não possui fotos.</div>
                            <?php endif;?>

                            <?php foreach($user->photos as $photo):?>
                                <div class="user-photo-item">
                                    <a href="#modal-<?=$photo->id;?>" rel="modal:open">
                                        <img src="<?=$base?>/media/uploads/<?=$photo->body;?>" />
                                    </a>
                                    <div id="modal-<?=$photo->id;?>" style="display:none">
                                        <img src="<?=$base;?>/media/uploads/<?=$photo->body;?>" />
                                    </div>
                                </div>
                            <?php endforeach;?>

                        </div>
                    </div>
                </div>
            </div>
        </div>    

    </section>

</section>
<?=$render("footer")?>