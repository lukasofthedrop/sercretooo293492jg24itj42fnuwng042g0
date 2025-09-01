/*
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import i18n from 'laravel-vue-i18n/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                base: null,
                includeAbsolute: false,
            }
        }),
        i18n()
    ],
    build: {
        chunkSizeWarningLimit: 2024,
    },
    resolve: {
        alias: {
            global: 'global',
        },
    },
    define: {
        global: 'window', // Garante que `global` seja tratado como `window` no navegador
    },
});
*/




import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import i18n from 'laravel-vue-i18n/vite';
import JavaScriptObfuscator from 'javascript-obfuscator';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                base: null,
                includeAbsolute: false,
            }
        }),
        i18n(),
        {
            name: 'vite-plugin-obfuscate',
            apply: 'build',
            enforce: 'post',
            generateBundle(options, bundle) {
                for (const file of Object.values(bundle)) {
                    if (file.type === 'chunk') {
                        const obfuscatedCode = JavaScriptObfuscator.obfuscate(file.code, {
                           compact: true, // Mantém o código compacto
                           stringArray: false, // Ofusca strings
                           stringArrayEncoding: ['base64'], // Usa codificação base64 para strings
                           stringArrayThreshold: 0.9, // Ofusca 50% das strings
                           splitStrings: false, // Divide strings em partes menores
                           splitStringsChunkLength: 30, // Aumenta o tamanho dos chunks de strings divididas
                           transformObjectKeys: false, // Desativa a ofuscação de chaves de objetos
                           renameGlobals: true, // Desativa a renomeação de variáveis globais
                           rotateStringArray: true, // Roda o array de strings periodicamente
                           debugProtection: true, // Adiciona proteção básica de depuração
                           debugProtectionInterval: 2000, // Verifica a cada 4 segundos se o DevTools está aberto
                        }).getObfuscatedCode();

                        file.code = obfuscatedCode;
                    }
                }
            },
        }
    ],
    build: {
        chunkSizeWarningLimit: 2024,
    }
});



