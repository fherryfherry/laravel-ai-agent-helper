<script>
    (function () {
        const getSidebarMenu = () => {
            const menus = [];
            document.querySelectorAll('aside nav a').forEach(a => {
                menus.push({
                    label: a.querySelector('span:not(.material-symbols-outlined)')?.textContent.trim(),
                    url: a.href,
                    active: a.classList.contains('bg-primary/10') || a.classList.contains('bg-primary')
                });
            });
            return menus;
        };

        const getFormInputs = () => {
            const forms = [];
            document.querySelectorAll('form').forEach(form => {
                const inputs = [];
                form.querySelectorAll('input, select, textarea').forEach(input => {
                    if (input.type === 'hidden') return;
                    inputs.push({
                        name: input.name,
                        type: input.type,
                        placeholder: input.placeholder,
                        value: input.value
                    });
                });
                forms.push({
                    action: form.action,
                    method: form.method,
                    inputs: inputs
                });
            });
            return forms;
        };

        const describeElement = (node) => {
            const element = node instanceof Element
                ? node
                : node?.parentElement instanceof Element
                    ? node.parentElement
                    : null;

            if (!element) return null;

            const classes = Array.from(element.classList).slice(0, 3);
            const selector = [
                element.tagName.toLowerCase(),
                element.id ? `#${element.id}` : '',
                classes.length ? `.${classes.join('.')}` : '',
            ].join('');

            return {
                tag: element.tagName.toLowerCase(),
                id: element.id || null,
                name: element.getAttribute('name'),
                type: element.getAttribute('type'),
                role: element.getAttribute('role'),
                classes,
                selector,
            };
        };

        const getSelectionMetadata = () => {
            const activeElement = document.activeElement;
            if (activeElement instanceof HTMLInputElement || activeElement instanceof HTMLTextAreaElement) {
                const start = activeElement.selectionStart ?? 0;
                const end = activeElement.selectionEnd ?? 0;
                const text = activeElement.value.slice(start, end).trim();
                if (text) {
                    return { text, element: describeElement(activeElement) };
                }
            }
            const selection = window.getSelection();
            if (!selection || selection.rangeCount === 0) return { text: '', element: null };
            const text = selection.toString().trim();
            if (!text) return { text: '', element: null };
            return {
                text,
                element: describeElement(selection.getRangeAt(0).commonAncestorContainer),
            };
        };

        const send = () => {
            try {
                const selection = getSelectionMetadata();
                const metadata = {
                    type: 'iframe-metadata',
                    url: window.location.href,
                    title: document.title,
                    controller_method: "{{ \Illuminate\Support\Facades\Route::currentRouteAction() }}",
                    route_name: "{{ \Illuminate\Support\Facades\Route::currentRouteName() }}",
                    view_blade: "{{ $active_view ?? 'unknown' }}",
                    sidebar_menus: getSidebarMenu(),
                    form_inputs: getFormInputs(),
                    project_name: "{{ config('app.name') }}",
                    user_name: "{{ auth()->user()->name ?? 'Guest' }}",
                    theme_mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                    selected_text: selection.text,
                    selected_element: selection.element,
                    flash_messages: {
                        success: "{{ session('success') }}",
                        error: "{{ session('error') }}"
                    }
                };
                window.parent.postMessage(metadata, '*');
            } catch (e) {
                console.error('Error sending metadata:', e);
            }
        };

        const wrapHistory = () => {
            const pushState = history.pushState;
            history.pushState = function () {
                pushState.apply(this, arguments);
                send();
            };
            const replaceState = history.replaceState;
            history.replaceState = function () {
                replaceState.apply(this, arguments);
                send();
            };
        };

        window.addEventListener('load', send);
        window.addEventListener('popstate', send);

        let selectionUpdateTimeout;
        const queueSelectionUpdate = () => {
            clearTimeout(selectionUpdateTimeout);
            selectionUpdateTimeout = setTimeout(send, 120);
        };

        document.addEventListener('click', (event) => {
            const anchor = event.target.closest('a');
            if (anchor && anchor.href) {
                setTimeout(send, 50);
            }
        });

        document.addEventListener('selectionchange', queueSelectionUpdate);
        document.addEventListener('select', queueSelectionUpdate, true);

        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'class') {
                    send();
                }
            });
        });
        observer.observe(document.documentElement, { attributes: true });

        wrapHistory();
        send();
    })();
</script>
