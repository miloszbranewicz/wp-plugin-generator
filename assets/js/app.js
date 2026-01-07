document.addEventListener('DOMContentLoaded', function() {
    const pluginNameInput = document.getElementById('plugin_name');
    const pluginSlugInput = document.getElementById('plugin_slug');
    const textDomainInput = document.getElementById('text_domain');
    const pluginNamespaceInput = document.getElementById('plugin_namespace');
    
    // Funkcja do generowania slug z nazwy
    function generateSlug(name) {
        return name
            .toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '') // Usuń znaki specjalne
            .replace(/\s+/g, '-')     // Zamień spacje na myślniki
            .replace(/-+/g, '-')      // Zamień wielokrotne myślniki na pojedyncze
            .replace(/^-|-$/g, '');   // Usuń myślniki z początku i końca
    }
    
    // Funkcja do generowania namespace z nazwy (PascalCase)
    function generateNamespace(name) {
        return name
            .trim()
            .replace(/[^\w\s]/g, '')  // Usuń znaki specjalne
            .split(/\s+/)             // Podziel na słowa
            .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
            .join('');                // Połącz w PascalCase
    }
    
    // Flagi do śledzenia, czy użytkownik ręcznie edytował pola
    let slugManuallyEdited = false;
    let textDomainManuallyEdited = false;
    let namespaceManuallyEdited = false;
    
    // Nasłuchuj zmian w polu nazwy wtyczki
    pluginNameInput.addEventListener('input', function() {
        const name = this.value;
        
        // Auto-generuj slug jeśli nie był ręcznie edytowany
        if (!slugManuallyEdited) {
            pluginSlugInput.value = generateSlug(name);
        }
        
        // Auto-generuj text domain jeśli nie był ręcznie edytowany
        if (!textDomainManuallyEdited) {
            textDomainInput.value = generateSlug(name);
        }
        
        // Auto-generuj namespace jeśli nie był ręcznie edytowany
        if (!namespaceManuallyEdited) {
            pluginNamespaceInput.value = generateNamespace(name);
        }
    });
    
    // Oznacz pola jako ręcznie edytowane gdy użytkownik je zmieni
    pluginSlugInput.addEventListener('input', function() {
        slugManuallyEdited = this.value !== generateSlug(pluginNameInput.value);
    });
    
    textDomainInput.addEventListener('input', function() {
        textDomainManuallyEdited = this.value !== generateSlug(pluginNameInput.value);
    });
    
    pluginNamespaceInput.addEventListener('input', function() {
        namespaceManuallyEdited = this.value !== generateNamespace(pluginNameInput.value);
    });
    
    // Walidacja formularza przed wysłaniem
    const form = document.getElementById('plugin-form');
    form.addEventListener('submit', function(e) {
        // Sprawdź czy wszystkie wymagane pola są wypełnione
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(function(field) {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        // Sprawdź pattern dla slug i text domain
        const slugPattern = /^[a-z0-9-]+$/;
        if (pluginSlugInput.value && !slugPattern.test(pluginSlugInput.value)) {
            pluginSlugInput.classList.add('is-invalid');
            isValid = false;
        }
        
        if (textDomainInput.value && !slugPattern.test(textDomainInput.value)) {
            textDomainInput.classList.add('is-invalid');
            isValid = false;
        }
        
        // Sprawdź pattern dla namespace
        const namespacePattern = /^[A-Za-z][A-Za-z0-9]*$/;
        if (pluginNamespaceInput.value && !namespacePattern.test(pluginNamespaceInput.value)) {
            pluginNamespaceInput.classList.add('is-invalid');
            isValid = false;
        }
        
        const vendorNamespaceInput = document.getElementById('vendor_namespace');
        if (vendorNamespaceInput.value && !namespacePattern.test(vendorNamespaceInput.value)) {
            vendorNamespaceInput.classList.add('is-invalid');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
    
    // Usuń klasę is-invalid przy focus
    const allInputs = form.querySelectorAll('input, textarea, select');
    allInputs.forEach(function(input) {
        input.addEventListener('focus', function() {
            this.classList.remove('is-invalid');
        });
    });
});
