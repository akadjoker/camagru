<?php include 'views/templates/header.php'; ?>

<section class="section">
    <div class="container">
        <h1 class="title">Editor de Imagens</h1>

        <div class="columns">
            <div class="column is-half">
                <h2 class="subtitle">Escolhe uma imagem</h2>

                <!-- Botão para ativar a câmara -->
                <button class="button is-link mb-2" onclick="startCamera()">Usar Webcam</button>
                <video id="cameraPreview" autoplay style="display: none; max-width: 100%;"></video>
                <canvas id="cameraCanvas" style="display: none;"></canvas>
                <button class="button is-primary mt-2" style="display:none;" id="captureButton" onclick="captureImage()">Capturar</button>

                <div class="box">
                    <form method="POST" enctype="multipart/form-data" action="?page=upload" id="editorForm">
                        <div class="field">
                            <label class="label">Upload de imagem</label>
                            <div class="file has-name is-fullwidth">
                                <label class="file-label">
                                    <input class="file-input" type="file" name="image" id="imageInput" required>
                                    <span class="file-cta">
                                        <span class="file-icon">
                                            <i class="fas fa-upload"></i>
                                        </span>
                                        <span class="file-label">
                                            Escolher ficheiro
                                        </span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <input type="hidden" name="overlay" id="overlayInput">
                        <input type="hidden" name="overlay_x" id="overlayX">
                        <input type="hidden" name="overlay_y" id="overlayY">
                        <input type="hidden" name="overlay_scale" id="overlayScale">

                        <div class="field">
                            <label class="label">Escolher Overlay</label>
                            <div class="buttons are-small is-multiline">
                                <?php
                                $overlays = [
                                    'frame' => '/assets/overlays/frame.png',
                                    'moldura' => '/assets/overlays/moldura.png',
                                    'fire' => '/assets/overlays/fire.png',
                                    'mirror' => '/assets/overlays/mirror.png',
                                    'sunglasses' => '/assets/overlays/sunglasses.png',
                                    'thug' => '/assets/overlays/thug.png',
                                    'hat' => '/assets/overlays/hat.png',
                                    'mustache' => '/assets/overlays/mustache.png',
                                    'cat' => '/assets/overlays/cat.png',
                                    'bear' => '/assets/overlays/bear.png',
                                    'witch' => '/assets/overlays/witch.png'
                                ];
                                foreach ($overlays as $name => $path): ?>
                                    <button type="button" class="button is-light" onclick="selectOverlay('<?= $path ?>')">
                                        <img src="<?= $path ?>" alt="<?= htmlspecialchars($name) ?>" style="height: 40px;">
                                        <span><?= htmlspecialchars($name) ?></span>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">Escolher Filtro</label>
                            <div class="select is-fullwidth">
                                <select name="filter" id="filterSelect" onchange="updatePreview()">
                                    <option value="">Nenhum</option>
                                    <option value="grayscale">Preto & Branco</option>
                                    <option value="sepia">Sépia</option>
                                    <option value="invert">Inverter cores</option>
                                    <option value="brightness">Brilho aumentado</option>
                                </select>
                            </div>
                        </div>

                        <div class="field">
                            <button class="button is-primary mt-2" type="submit">Gravar imagem</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="column">
                <h2 class="subtitle">Pré-visualização</h2>
                <div style="position: relative; display: inline-block; border: 1px solid #ccc;">
                    <img id="previewImage" src="" alt="Preview" style="max-width: 100%; display: none;">
                    <img id="previewOverlay" src="" 
                        style="position: absolute; top: 0; left: 0; width: 100px; cursor: move; display: none;">
                </div>
            </div>
        </div>

        <hr>

        <h2 class="title is-4">As tuas últimas imagens</h2>
        <div class="columns is-multiline">
            <?php if (empty($recentImages)): ?>
                <p>Ainda não tens imagens.</p>
            <?php else: ?>
                <?php foreach ($recentImages as $img): ?>
                    <div class="column is-3">
                        <div class="card">
                            <div class="card-image">
                                <figure class="image is-square">
                                    <img src="<?= htmlspecialchars($img['filename']) ?>" alt="Imagem">
                                </figure>
                            </div>
                            <div class="card-content has-text-centered">
                            <button class="button is-danger is-small"
                                onclick="openGenericModal(
                                    'Tens a certeza que queres apagar esta imagem?',
                                    '?page=delete_editor_image',
                                    {'image_id': <?= $img['id'] ?>}
                                )">
                                <span class="icon"><i class="fas fa-trash"></i></span>
                            </button>

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
const imageInput = document.getElementById('imageInput');
const overlayInput = document.getElementById('overlayInput');
const filterSelect = document.getElementById('filterSelect');
const previewImage = document.getElementById('previewImage');
const previewOverlay = document.getElementById('previewOverlay');
const overlayXInput = document.getElementById('overlayX');
const overlayYInput = document.getElementById('overlayY');
const overlayScaleInput = document.getElementById('overlayScale');

// Preview de imagem
imageInput.addEventListener('change', function () {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            previewImage.src = e.target.result;
            previewImage.style.display = 'block';
            updatePreview();
        };
        reader.readAsDataURL(file);
    }
});

function updatePreview() {
    const filter = filterSelect.value;
    const filterStyle = {
        grayscale: 'grayscale(100%)',
        sepia: 'sepia(100%)',
        invert: 'invert(100%)',
        brightness: 'brightness(150%)'
    }[filter] || 'none';

    previewOverlay.style.filter = filterStyle;
    previewImage.style.filter = filterStyle;
}

function selectOverlay(path) {
    previewOverlay.src = path;
    previewOverlay.style.display = 'block';
    overlayInput.value = path;
    previewOverlay.style.left = '0px';
    previewOverlay.style.top = '0px';
    previewOverlay.style.width = '100px';
    overlayXInput.value = 0;
    overlayYInput.value = 0;
    overlayScaleInput.value = 100;
}

let offsetX = 0, offsetY = 0, isDragging = false;

previewOverlay.addEventListener('mousedown', function(e) {
    isDragging = true;
    offsetX = e.offsetX;
    offsetY = e.offsetY;
});

document.addEventListener('mousemove', function(e) {
    if (!isDragging) return;
    const rect = previewImage.getBoundingClientRect();
    let left = e.clientX - rect.left - offsetX;
    let top = e.clientY - rect.top - offsetY;

    left = Math.max(0, Math.min(left, previewImage.width - previewOverlay.width));
    top = Math.max(0, Math.min(top, previewImage.height - previewOverlay.height));

    previewOverlay.style.left = left + 'px';
    previewOverlay.style.top = top + 'px';
    overlayXInput.value = left;
    overlayYInput.value = top;
});

document.addEventListener('mouseup', () => isDragging = false);

previewOverlay.addEventListener('wheel', function(e) {
    e.preventDefault();
    let newWidth = previewOverlay.width + (e.deltaY < 0 ? 10 : -10);
    newWidth = Math.max(30, Math.min(newWidth, previewImage.width));
    previewOverlay.style.width = newWidth + 'px';
    overlayScaleInput.value = newWidth;
});

// ------------------ CAMERA ----------------------

let stream;

function startCamera() {
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(function (mediaStream) {
            stream = mediaStream;
            const video = document.getElementById('cameraPreview');
            video.srcObject = mediaStream;
            video.style.display = 'block';
            document.getElementById('captureButton').style.display = 'inline-block';
        })
        .catch(function (err) {
            alert("Não foi possível aceder à câmara: " + err);
        });
}

function captureImage() {
    const video = document.getElementById('cameraPreview');
    const canvas = document.getElementById('cameraCanvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);

    canvas.toBlob(function(blob) {
        const file = new File([blob], "captured.png", { type: "image/png" });
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        imageInput.files = dataTransfer.files;

        const reader = new FileReader();
        reader.onload = function (e) {
            previewImage.src = e.target.result;
            previewImage.style.display = 'block';
            updatePreview();
        };
        reader.readAsDataURL(file);
    });

    stream.getTracks().forEach(track => track.stop());
    video.style.display = 'none';
    document.getElementById('captureButton').style.display = 'none';
}

function openGenericModal(message, formAction, hiddenFields) {
    document.getElementById('genericModalMessage').textContent = message;
    const form = document.getElementById('genericModalForm');
    form.action = formAction;

    // Limpa inputs antigos
    form.querySelectorAll('input[type=hidden]').forEach(e => e.remove());

    for (const [name, value] of Object.entries(hiddenFields)) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
    }

    document.getElementById('genericModal').classList.add('is-active');
}

function closeGenericModal() {
    document.getElementById('genericModal').classList.remove('is-active');
}
</script>


<div class="modal" id="genericModal">
  <div class="modal-background"></div>
  <div class="modal-content">
    <div class="box has-text-centered">
      <p id="genericModalMessage" class="title is-5">Tens a certeza que queres apagar esta imagem?</p>
      <form method="POST" id="genericModalForm">
        <!-- Campos escondidos serão adicionados via JS -->
        <button type="submit" class="button is-danger">Confirmar</button>
        <button type="button" class="button is-light" onclick="closeGenericModal()">Cancelar</button>
      </form>
    </div>
  </div>
  <button class="modal-close is-large" aria-label="close" onclick="closeGenericModal()"></button>
</div>


<?php include 'views/templates/footer.php'; ?>
