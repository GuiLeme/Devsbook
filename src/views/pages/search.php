<?=$render('header', ['loggedUser' => $loggedUser]);?>
<?php $activeMenu = 'none'?>
<section class="container main">
    <?php if($user->id === $loggedUser->id):?>
        <?php $activeMenu = 'profile';?>
    <?php endif;?>
    <?=$render('sidebar', ['activeMenu' => $activeMenu]);?>
    
    
    <section class="feed mt-10">
        <div class="row">
            <div class="column pr-5">

            <div class="search-term">Você pesquisou: <?=$searchTerm;?></div>
                                
                <div class="full-friend-list estilo-lista mt-10">
                    <?php foreach($users as $user):?>
                        <div class="friend-icon">
                            <a href="<?=$base?>/perfil/<?=$user->id;?>">
                                <div class="friend-icon-avatar">
                                    <img src="<?=$base;?>/media/avatars/<?=$user->avatar;?>" />
                                </div>
                                <div class="friend-icon-name">
                                    <?=$user->name;?>
                                </div>
                            </a>
                        </div>
                    <?php endforeach;?>
                </div>
            </div>
            <div class="column side pl-5">
                <?=$render('right-side');?>
            </div>
        </div>
    </section>

</section>
<?=$render("footer")?>