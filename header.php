<header>
    <div class="logo">CIETEC</div>
    <nav>
        <ul>
            <?php $pagina_atual = basename($_SERVER['PHP_SELF']); ?>
            
            <li><a href="index.php" class="<?= $pagina_atual == 'index.php' ? 'active' : '' ?>">Calendário</a></li>
            <li><a href="mensagens.php" class="<?= $pagina_atual == 'mensagens.php' ? 'active' : '' ?>">Mensagens</a></li>
            <li><a href="perfil.php" class="<?= $pagina_atual == 'perfil.php' ? 'active' : '' ?>">Perfil</a></li>
            
            <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                <li><a href="admin.php" class="<?= $pagina_atual == 'admin.php' ? 'active' : '' ?>" style="color: #f08a9b;">Painel Admin</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <div class="user-info">
        Olá, <strong><?= htmlspecialchars($_SESSION['nome']) ?></strong>! 
        <a href="logout.php" style="margin-left: 15px; color: #f08a9b; text-decoration: none;">Sair</a>
    </div>
</header>