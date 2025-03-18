 
<div class="container">
    <h1 class="title is-2 has-text-centered">Editor de Imagens</h1>
    
    <div class="columns">
        <div class="column is-two-thirds">
            <div class="box">
                <h2 class="title is-4">Capturar Imagem</h2>
                
                <div class="tabs">
                    <ul>
                        <li class="is-active" id="tab-webcam"><a>Webcam</a></li>
                        <li id="tab-upload"><a>Upload</a></li>
                    </ul>
                </div>
                
                <!-- Tab Webcam -->
                <div id="content-webcam">
                    <div class="has-text-centered mb-4">
                        <video id="video" width="100%" height="auto" autoplay></video>
                        <canvas id="canvas" style="display:none;"></canvas>
                    </div>
                    
                    <div class="field">
                        <div class="control">
                            <button id="snap" class="button is-primary is-fullwidth" disabled>
                                <span class="icon"><i class="fas fa-camera"></i></span>
                                <span>Tirar Foto</span>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Tab Upload -->
                <div id="content-upload" style="display:none;">
                    <form id="upload-form">
                        <div class="file has-name is-fullwidth mb-4">
                            <label class="file-label">
                                <input class="file-input" type="file" name="file_image" id="file-input" accept="image/*">
                                <span class="file-cta">
                                    <span class="file-icon">
                                        <i class="fas fa-upload"></i>
                                    </span>
                                    <span class="file-label">
                                        Escolher ficheiro
                                    </span>
                                </span>
                                <span class="file-name" id="file-name">
                                    Nenhum ficheiro selecionado
                                </span>
                            </label>
                        </div>
                        
                        <div class="field">
                            <div class="control">
                                <button type="submit" class="button is-primary is-fullwidth" id="upload-button" disabled>
                                    <span class="icon"><i class="fas fa-upload"></i></span>
                                    <span>Enviar Imagem</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Resultado da captura -->
            <div class="box" id="result-box" style="display:none;">
                <h2 class="title is-4">Resultado</h2>
                
                <div class="has-text-centered mb-4">
                    <img id="result-image" src="" alt="Imagem capturada" style="max-width:100%;">
                </div>
                
                <div class="field is-grouped">
                    <div class="control is-expanded">
                        <button id="save-button" class="button is-primary is-fullwidth">
                            <span class="icon"><i class="fas fa-save"></i></span>
                            <span>Guardar na Galeria</span>
                        </button>
                    </div>
                    <div class="control">
                        <button id="cancel-button" class="button is-light">
                            <span class="icon"><i class="fas fa-times"></i></span>
                            <span>Cancelar</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="column">
            <!-- Sobreposições -->
            <div class="box">
                <h2 class="title is-4">Sobreposições</h2>
                
                <?php if (empty($overlays)): ?>
                    <p class="has-text-centered">Não existem sobreposições disponíveis.</p>
                <?php else: ?>
                    <div class="field">
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select id="overlay-select">
                                    <option value="">Seleciona uma sobreposição</option>
                                    <?php foreach ($overlays as $overlay): ?>
                                        <option value="<?= $overlay['id'] ?>"><?= htmlspecialchars($overlay['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div id="overlay-preview" class="has-text-centered mt-4" style="display:none;">
                        <img id="overlay-image" src="" alt="Sobreposição" style="max-width:100%;">
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Filtros -->
            <div class="box">
                <h2 class="title is-4">Filtros</h2>
                
                <div class="field">
                    <div class="control">
                        <div class="select is-fullwidth">
                            <select id="filter-select">
                                <option value="">Sem filtro</option>
                                <option value="grayscale">Preto e Branco</option>
                                <option value="sepia">Sépia</option>
                                <option value="invert">Inverter Cores</option>
                                <option value="brightness">Aumentar Brilho</option>
                                <option value="contrast">Aumentar Contraste</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Minhas Imagens -->
            <div class="box">
                <h2 class="title is-4">Minhas Imagens</h2>
                
                <?php if (empty($userImages)): ?>
                    <p class="has-text-centered">Ainda não tens imagens.</p>
                <?php else: ?>
                    <div class="user-images">
                        <?php foreach ($userImages as $image): ?>
                            <div class="user-image mb-3">
                                <a href="/?controller=gallery&action=view&id=<?= $image['id'] ?>">
                                    <img src="<?= $image['filepath'] ?>" alt="Imagem do utilizador" style="max-width:100%;">
                                </a>
                                <div class="buttons is-centered mt-2">
                                    <a href="/?controller=gallery&action=view&id=<?= $image['id'] ?>" class="button is-small is-link">
                                        <span class="icon"><i class="fas fa-eye"></i></span>
                                    </a>
                                    <!-- <button class="button is-small is-danger" 
                                            onclick="if(confirm('Tens a certeza que queres apagar esta imagem?')) { 
                                                window.location.href='/?controller=editor&action=delete&id=<?= $image['id'] ?>'
                                            }">
                                        <span class="icon"><i class="fas fa-trash"></i></span>
                                    </button> -->

                                    <button class="button is-small is-danger" 
                                            onclick="showConfirm('Apagar Imagem', 'Tens a certeza que queres apagar esta imagem?', function() { 
                                                window.location.href='/?controller=editor&action=delete&id=<?= $image['id'] ?>'
                                            })">
                                        <span class="icon"><i class="fas fa-trash"></i></span>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() 
{

    
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const snap = document.getElementById('snap');
    const resultImage = document.getElementById('result-image');
    const resultBox = document.getElementById('result-box');
    const saveButton = document.getElementById('save-button');
    const cancelButton = document.getElementById('cancel-button');
    const fileInput = document.getElementById('file-input');
    const fileName = document.getElementById('file-name');
    const uploadButton = document.getElementById('upload-button');
    const uploadForm = document.getElementById('upload-form');
    const overlaySelect = document.getElementById('overlay-select');
    const overlayPreview = document.getElementById('overlay-preview');
    const overlayImage = document.getElementById('overlay-image');
    const tabWebcam = document.getElementById('tab-webcam');
    const tabUpload = document.getElementById('tab-upload');
    const contentWebcam = document.getElementById('content-webcam');
    const contentUpload = document.getElementById('content-upload');
 

    
    let capturedImage = null;
    let selectedOverlayId = null;
 
    

    
    let stream = null;
    async function startWebcam() 
    {
        try 
        {
            stream = await navigator.mediaDevices.getUserMedia({ video: true });
            video.srcObject = stream;
            snap.disabled = false;
        } catch (err) 
        {
            console.error('Erro ao aceder à webcam:', err);
            //alert('Não foi possível aceder à webcam. Verifica as permissões do navegador.');
            showMessage('Erro', 'Erro ao aceder à webcam. Verifica as permissões do navegador.', 'error');
        }
    }
    
    // Parar a webcam
    function stopWebcam() 
    {
        if (stream) 
        {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
    }
    
    // Trocar entre tabs
    tabWebcam.addEventListener('click', function() 
    {
        tabWebcam.classList.add('is-active');
        tabUpload.classList.remove('is-active');
        contentWebcam.style.display = '';
        contentUpload.style.display = 'none';
        startWebcam();
    });
    
    tabUpload.addEventListener('click', function() 
    {
        tabUpload.classList.add('is-active');
        tabWebcam.classList.remove('is-active');
        contentUpload.style.display = '';
        contentWebcam.style.display = 'none';
        stopWebcam();
    });
    
    // Iniciar a webcam quando a página carrega
    startWebcam();
    
    // Capturar imagem da webcam
    snap.addEventListener('click', function()
     {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        capturedImage = canvas.toDataURL('image/png');
        resultImage.src = capturedImage;
        resultBox.style.display = '';
    });
    
    // Evento de mudança no input de ficheiro
    fileInput.addEventListener('change', function() 
    {
        if (fileInput.files.length > 0) 
        {
            fileName.textContent = fileInput.files[0].name;
            uploadButton.disabled = false;
        } else {
            fileName.textContent = 'Nenhum ficheiro selecionado';
            uploadButton.disabled = true;
        }
    });

    // Para webcam
saveButton.addEventListener('click', function() {
    if (!capturedImage) {
        showMessage('Erro', 'Nenhuma imagem capturada.', 'error');
        return;
    }
    
    const formData = new FormData();
    formData.append('webcam_image', capturedImage);
    
    if (selectedOverlayId) {
        formData.append('overlay_id', selectedOverlayId);
    }
    
    // Adiciona o filtro se selecionado
    const filterSelect = document.getElementById('filter-select');
    if (filterSelect && filterSelect.value) {
        formData.append('filter', filterSelect.value);
    }
    
    fetch('/?controller=editor&action=upload', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())  // Alterado para text() em vez de json()
    .then(data => {
        console.log("Resposta do servidor:", data);  // Log para debug
        if (data === "success") {
            showMessage('Sucesso', 'Imagem guardada com sucesso!', 'success');
            setTimeout(() => { location.reload(); }, 1500);
        } else {
            showMessage('Erro', 'Ocorreu um erro ao guardar a imagem.', 'error');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showMessage('Erro', 'Ocorreu um erro ao guardar a imagem.', 'error');
    });
});

// Para upload de arquivo
uploadForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (fileInput.files.length === 0) {
        showMessage('Erro', 'Por favor, seleciona um ficheiro.', 'error');
        return;
    }
    
    const formData = new FormData();
    formData.append('file_image', fileInput.files[0]);
    
    if (selectedOverlayId) {
        formData.append('overlay_id', selectedOverlayId);
    }
    
    // Adiciona o filtro se selecionado
    const filterSelect = document.getElementById('filter-select');
    if (filterSelect && filterSelect.value) {
        formData.append('filter', filterSelect.value);
    }
    
    fetch('/?controller=editor&action=upload', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())  // Alterado para text() em vez de json()
    .then(data => {
        console.log("Resposta do servidor:", data);  // Log para debug
        if (data === "success") {
            showMessage('Sucesso', 'Imagem guardada com sucesso!', 'success');
            setTimeout(() => { location.reload(); }, 1500);
        } else {
            showMessage('Erro', 'Ocorreu um erro ao guardar a imagem.', 'error');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showMessage('Erro', 'Ocorreu um erro ao enviar a imagem.', 'error');
    });
});
    

    
    // Cancelar captura
    cancelButton.addEventListener('click', function() 
    {
        capturedImage = null;
        resultBox.style.display = 'none';
    });
    
    // Seleção de sobreposição
    overlaySelect.addEventListener('change', function() 
    {
        selectedOverlayId = this.value;
        
        if (selectedOverlayId) 
        {
  
            <?php foreach ($overlays as $overlay): ?>
                if (selectedOverlayId === '<?= $overlay['id'] ?>') 
                {
                    overlayImage.src = '<?= $overlay['filepath'] ?>';
                    overlayPreview.style.display = '';
                }
            <?php endforeach; ?>
        } else 
        {
            overlayPreview.style.display = 'none';
        }
    });
});
</script>