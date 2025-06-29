document.addEventListener('DOMContentLoaded', function() {
    const forms = ["contact-form-home", "contact-form-page"];

    forms.forEach(formId => {
        const form = document.getElementById(formId);
        if (!form) return;
        form.addEventListener("submit", function(e) {
            e.preventDefault();

            const name = form.querySelector("#name")?.value.trim();
            const email = form.querySelector("#email")?.value.trim();
            const phone = form.querySelector("#phone")?.value.trim();
            const subject = form.querySelector("#subject")?.value.trim();
            const message = form.querySelector("#message")?.value.trim();

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const phoneRegex = /^[0-9\-\+\s\(\)]{8,20}$/;

            if(!name || !email || !subject || !message) {
                showModal("Lütfen tüm zorunlu alanları doldurunuz.");
                return;
            }

            if (!emailRegex.test(email)) {
                showModal("Lütfen geçerli bir e-posta adresi giriniz.");
                return;
            }

            if (phone && !phoneRegex.test(phone)) {
                showModal("Lütfen geçerli bir telefon numarası giriniz.");
                return;
            }

            const formData = new FormData(form);

            fetch(location.origin + '/public/api/formhandler.php', {
                method: "POST",
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    showModal(data.message);
                    if(data.status === "success") {
                        form.reset();
                    }
                })
                .catch(() => {
                    showModal("Gönderim sırasında bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.");
                });
        });
    });
});

function showModal(message) {
  document.getElementById('feedbackModalMessage').textContent = message;

  var feedbackModal = new bootstrap.Modal(document.getElementById('feedbackModal'), {
    keyboard: false
  });

  feedbackModal.show();
}