<?php
// Decodificar JSONs
$criterios_rol = $alerta['criterios_rol'] ? json_decode($alerta['criterios_rol'], true) : [];
$botones = $alerta['botones'] ? json_decode($alerta['botones'], true) : [];
?>

<div class="titulo-boton mt-4">
    <h1 class="titulo-modulo">Gestión de Alertas</h1>
</div>

<div class="my-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-chevron p-3 bg-body-tertiary rounded-3">
            <li class="breadcrumb-item">
                <a class="link-body-emphasis" href="<?= $url . 'admin_alertas' ?>">
                    <i class="fas fa-home" style="color: black;"></i>
                    <span class="visually-hidden">Home</span>
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                Ver alerta
            </li>
        </ol>
    </nav>
</div>

<h6 class="subtitulo mt-3">Información de la alerta</h6>
<div class="caja">
    <div class="row mt-3">
        <div class="col-md-12 text-end mb-3">
            <a href="<?= $url . 'admin_alertas/' . $alerta['id'] . '/editar' ?>" class="btn btn-primary">Editar</a>
            <a href="<?= $url . 'admin_alertas' ?>" class="btn btn-secondary">Volver</a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label><strong>Código:</strong></label>
                <p><?= htmlspecialchars($alerta['codigo']) ?></p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label><strong>Estado:</strong></label>
                <p>
                    <?php if ($alerta['activa'] == 1): ?>
                        <span class="badge bg-success">Activa</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Inactiva</span>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label><strong>Título:</strong></label>
                <p><?= htmlspecialchars($alerta['titulo']) ?></p>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label><strong>Mensaje:</strong></label>
                <p><?= nl2br(htmlspecialchars($alerta['mensaje'])) ?></p>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label><strong>Tipo:</strong></label>
                <p><span class="badge bg-<?= $alerta['tipo'] ?>"><?= ucfirst($alerta['tipo']) ?></span></p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label><strong>Icono:</strong></label>
                <p><?= $alerta['icono'] ? '<i class="' . htmlspecialchars($alerta['icono']) . '"></i> ' . htmlspecialchars($alerta['icono']) : 'Sin icono' ?></p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label><strong>Plantilla:</strong></label>
                <p><?= ucfirst($alerta['plantilla']) ?></p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label><strong>Prioridad:</strong></label>
                <p><?= $alerta['prioridad'] ?></p>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label><strong>Tipo de mostrar:</strong></label>
                <p>
                    <?php
                    switch($alerta['tipo_mostrar']) {
                        case 'una_vez':
                            echo 'Una vez';
                            break;
                        case 'n_veces':
                            echo $alerta['veces_mostrar'] . ' veces';
                            break;
                        case 'mientras_activa':
                            echo 'Mientras esté activa';
                            break;
                        default:
                            echo $alerta['tipo_mostrar'];
                    }
                    ?>
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label><strong>Fecha inicio:</strong></label>
                <p><?= $alerta['fecha_inicio'] ? date('d/m/Y H:i', strtotime($alerta['fecha_inicio'])) : 'Sin límite' ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label><strong>Fecha fin:</strong></label>
                <p><?= $alerta['fecha_fin'] ? date('d/m/Y H:i', strtotime($alerta['fecha_fin'])) : 'Sin límite' ?></p>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label><strong>Roles aplicables:</strong></label>
                <p>
                    <?php if (empty($criterios_rol)): ?>
                        Todos los roles
                    <?php else: ?>
                        <?= implode(', ', $criterios_rol) ?>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
    
    <?php if ($alerta['html_personalizado']): ?>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label><strong>HTML Personalizado:</strong></label>
                <pre class="bg-light p-3 rounded"><code><?= htmlspecialchars($alerta['html_personalizado']) ?></code></pre>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($alerta['estilo_personalizado']): ?>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label><strong>CSS Personalizado:</strong></label>
                <pre class="bg-light p-3 rounded"><code><?= htmlspecialchars($alerta['estilo_personalizado']) ?></code></pre>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($botones)): ?>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label><strong>Botones configurables:</strong></label>
                <ul class="list-group">
                    <?php foreach ($botones as $boton): ?>
                        <li class="list-group-item">
                            <strong><?= htmlspecialchars($boton['texto']) ?></strong> 
                            (<?= htmlspecialchars($boton['tipo']) ?>)
                            <?php if ($boton['url']): ?>
                                - URL: <?= htmlspecialchars($boton['url']) ?>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label><strong>Fecha de alta:</strong></label>
                <p><?= date('d/m/Y H:i', strtotime($alerta['fecha_alta'])) ?></p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label><strong>ID de alta:</strong></label>
                <p><?= $alerta['id_alta'] ?></p>
            </div>
        </div>
    </div>
</div>

