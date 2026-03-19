<div style="padding: 100px 40px 40px 40px; color: white;"> <h1 style="color: #c6a87d; font-family: 'Playfair Display'; font-size: 4rem; text-align: center; margin-bottom: 20px; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">
        <?php echo strtoupper($tipo); ?>
    </h1>

    <div style="margin-bottom: 30px; text-align: center;">
        <h2 style="color: #f77f00; font-family: 'League Spartan'; font-size: 1.8rem;">
            <?php echo ($tipo == 'flora') ? 'Especies Vegetales de Colima' : 'Vida Animal en Colima'; ?>
        </h2>
    </div>

    <div style="width: 100%; height: 300px; background: #333; border: 1px solid #444; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-bottom: 50px;">
        <span style="color: #888; font-family: 'League Spartan'; font-weight: bold;">[ RECUADRO GRIS ]</span>
    </div>

    <div class="consejos-container">
        <h3 style="color: #c6a87d; border-bottom: 1px solid rgba(198,168,125,0.3); padding-bottom: 10px; margin-bottom: 25px; font-family: 'Playfair Display'; letter-spacing: 1px;">
            CONSEJOS ALEATORIOS Y NO REPETITIVOS
        </h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <?php if($resultado_consejos && $resultado_consejos->num_rows > 0): ?>
                <?php while($row = $resultado_consejos->fetch_assoc()): ?>
                    <div style="background: rgba(255,255,255,0.05); padding: 25px; border-left: 4px solid #f77f00; border-radius: 5px;">
                        <h4 style="color: #fff; font-family: 'League Spartan'; margin-bottom: 10px; font-size: 1.3rem;">
                            <?php echo $row['titulo']; ?>
                        </h4>
                        <p style="color: #bbb; font-size: 1rem; line-height: 1.5; font-family: 'League Spartan';">
                            <?php echo $row['contenido']; ?>
                        </p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="color: #666;">No hay consejos cargados para esta categoría.</p>
            <?php endif; ?>
        </div>
    </div>

    <div style="text-align: center; margin-top: 60px;">
        <a href="home.php" style="color: #c6a87d; text-decoration: none; border: 2px solid #c6a87d; padding: 12px 40px; font-weight: bold; font-family: 'League Spartan'; text-transform: uppercase; transition: 0.3s;">
            VOLVER AL INICIO
        </a>
    </div>
</div>