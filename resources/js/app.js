// Utilitas floating alert & toast untuk feedback interaksi Dapur Bucik
window.showBucikToast = function (message, type = 'success') {
    const stack = document.getElementById('bucik-alert-stack');
    if (stack) {
        const isError = type === 'error';
        const isInfo = type === 'info';
        const borderColor = isError
            ? 'border-l-red-600'
            : isInfo
              ? 'border-l-[#E29E31]'
              : 'border-l-[#1B362D]';
        const badgeBg = isError
            ? 'bg-red-100 text-red-700'
            : isInfo
              ? 'bg-[#E29E31]/20 text-[#1B362D]'
              : 'bg-emerald-100 text-emerald-800';
        const titleText = isError ? 'Perhatian' : isInfo ? 'Informasi Dapur' : 'Berhasil';

        const alertEl = document.createElement('div');
        alertEl.setAttribute('role', 'alert');
        alertEl.className = `pointer-events-auto bucik-card bg-white text-[#1B362D] border border-[#1B362D]/20 border-l-4 ${borderColor} shadow-[0_10px_28px_rgba(27,54,45,0.18)] p-4 flex items-start gap-3 transition-all duration-300 opacity-0 -translate-y-2`;
        alertEl.innerHTML = `
            <span class="w-7 h-7 rounded-full ${badgeBg} flex items-center justify-center shrink-0 text-xs font-extrabold mt-0.5">
                ${isError ? '!' : '&#10003;'}
            </span>
            <div class="flex-1 min-w-0">
                <p class="font-serif text-xs font-bold uppercase tracking-wider text-[#1B362D]/70">${titleText}</p>
                <p class="text-xs sm:text-sm font-semibold text-[#1B362D] mt-0.5 break-words"></p>
            </div>
            <button type="button" aria-label="Tutup Notifikasi" class="text-[#1B362D]/45 hover:text-[#1B362D] text-lg leading-none font-bold px-1 cursor-pointer">&times;</button>
        `;

        const msgNode = alertEl.querySelector('p:last-child');
        if (msgNode) msgNode.textContent = message;

        const closeBtn = alertEl.querySelector('button');
        const dismiss = () => {
            alertEl.classList.add('opacity-0', '-translate-y-2');
            setTimeout(() => alertEl.remove(), 250);
        };
        closeBtn?.addEventListener('click', dismiss);

        stack.appendChild(alertEl);
        requestAnimationFrame(() => {
            alertEl.classList.remove('opacity-0', '-translate-y-2');
        });

        setTimeout(dismiss, 4200);
        return;
    }

    const toast = document.getElementById('bucik-toast');
    if (!toast) return;

    const textEl = toast.querySelector('[data-toast-message]');
    if (textEl) {
        textEl.textContent = message;
    }

    toast.classList.remove('opacity-0', 'translate-y-3', 'pointer-events-none');
    toast.classList.add('opacity-100', 'translate-y-0');

    clearTimeout(window.__bucikToastTimer);
    window.__bucikToastTimer = setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-y-3', 'pointer-events-none');
        toast.classList.remove('opacity-100', 'translate-y-0');
    }, 2600);
};

// Auto-dismiss floating alerts yang dirender dari session flash
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-bucik-alert]').forEach((el, index) => {
        const closeBtn = el.querySelector('[data-bucik-alert-close]');
        const dismiss = () => {
            el.classList.add('opacity-0', 'translate-x-4');
            setTimeout(() => el.remove(), 250);
        };
        closeBtn?.addEventListener('click', dismiss);
        setTimeout(dismiss, 5000 + index * 600);
    });
});
