<?php
/* @var $this yii\web\View */

$this->title = 'Qnits URL Shortener';
?>

<h1>Qnits URL Shortener</h1>

<form id="urlForm">
    <div class="form-group">
        <label for="originalUrl">Введите ссылку:</label>
        <input type="url" id="originalUrl" name="original_url" 
                placeholder="https://example.ru" required>
    </div>
    
    <button type="submit" id="submitBtn">
        <span class="btn-text">Создать короткую ссылку</span>
        <span class="btn-loading" style="display: none;">
            <span class="spinner"></span>Создание...
        </span>
    </button>
</form>

<div id="result" class="result"></div>

<script>
    document.getElementById('urlForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submitBtn');
        const btnText = submitBtn.querySelector('.btn-text');
        const btnLoading = submitBtn.querySelector('.btn-loading');
        const resultDiv = document.getElementById('result');
        const originalUrl = document.getElementById('originalUrl').value;
        
        submitBtn.disabled = true;
        btnText.style.display = 'none';
        btnLoading.style.display = 'inline';
        resultDiv.style.display = 'none';
        
        try {
            const response = await fetch('/api/url/create?original_url=' + encodeURIComponent(originalUrl), {
                method: 'GET'
            });
            
            const data = await response.json();
            
            if (data.success) {
                resultDiv.className = 'result success';
                resultDiv.innerHTML = `
                    <div class="short-url">
                        <strong>Короткая ссылка:</strong><br>
                        <a href="${data.short_url}" target="_blank">${data.short_url}</a>
                    </div>
                    <button class="copy-btn" onclick="copyToClipboard('${data.short_url}')">
                        Копировать
                    </button>
                `;
            } else {
                resultDiv.className = 'result error';
                resultDiv.innerHTML = `<strong>Ошибка:</strong> ${data.error}`;
            }
        } catch (error) {
            resultDiv.className = 'result error';
            resultDiv.innerHTML = `<strong>Ошибка:</strong> Не удалось создать короткую ссылку`;
        } finally {
            resultDiv.style.display = 'block';
            submitBtn.disabled = false;
            btnText.style.display = 'inline';
            btnLoading.style.display = 'none';
        }
    });
    
    function copyToClipboard(text) {
        // Пробуем современный Clipboard API
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Ссылка скопирована в буфер обмена!');
            }).catch(function(err) {
                console.error('Ошибка Clipboard API: ', err);
                // Fallback на старый метод
                fallbackCopyTextToClipboard(text);
            });
        } else {
            // Fallback для старых браузеров
            fallbackCopyTextToClipboard(text);
        }
    }
    
    function fallbackCopyTextToClipboard(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            const successful = document.execCommand('copy');
            if (successful) {
                alert('Ссылка скопирована в буфер обмена!');
            } else {
                alert('Не удалось скопировать ссылку. Скопируйте вручную: ' + text);
            }
        } catch (err) {
            console.error('Ошибка fallback копирования: ', err);
            alert('Не удалось скопировать ссылку. Скопируйте вручную: ' + text);
        }
        
        document.body.removeChild(textArea);
    }
</script> 