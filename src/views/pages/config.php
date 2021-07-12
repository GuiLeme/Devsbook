<?=$render('header', ['loggedUser' => $loggedUser]);?>
<?php 
    if ($userInfo->work === 'none'){
        $userInfo->work = '';
    }
    if ($userInfo->city === 'none'){
        $userInfo->city = '';
    }
?>
<section class="container main">
    <?=$render('sidebar', ['activeMenu' => 'config']);?>
    
    
    <section class="feed mt-10">
        <h1>Configurações:</h1>
        <div class="row">
            

            <form action="<?=$base;?>/config/action" method="POST" enctype="multipart/form-data" class="config-form">
                <label >
                    Novo avatar:
                    <input type="file" name="avatar">
                </label>
                
                <label >
                    Nova capa de perfil:
                    <input type="file" name="cover">
                </label>

                <hr>

                <label>
                    Nome Completo:
                    <input type="text" name="name" value="<?=$userInfo->name?>">
                </label>

                <label>
                    Data de nascimento:
                    <input type="date" name="birthdate" value="<?=$userInfo->birthdate?>">
                </label>

                <label>
                    Onde você trabalha:
                    <input type="text" name="work" value="<?=$userInfo->work?>">
                </label>

                <label>
                    Onde você mora:
                    <input type="text" name="city" value="<?=$userInfo->city?>">
                </label>

                <label>
                    Seu email:
                    <input type="email" name="email" value="<?=$userInfo->email?>">
                </label>

                <input class="btn-submit" type="submit">
            </form>
        </div>
    </section>

</section>
<?=$render("footer")?>