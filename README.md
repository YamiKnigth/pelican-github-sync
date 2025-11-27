# GitHub Sync Plugin para Pelican Panel

Plugin para sincronizar archivos del servidor de Pelican Panel con un repositorio de GitHub.

## 📋 Características

- ✅ Configuración de repositorio Git por servidor
- ✅ Operaciones Git: Clone, Pull y Push
- ✅ Tokens de acceso personal (PAT) encriptados
- ✅ Interfaz integrada en el panel de Filament
- ✅ Comandos ejecutados directamente en el servidor Wings

## 🚀 Instalación

### 1. Copiar el plugin

Copia la carpeta `githubsync` a tu directorio de plugins de Pelican:

```bash
cp -r githubsync /var/www/pelican/app/Plugins/githubsync
```

O clónalo directamente:

```bash
cd /var/www/pelican/app/Plugins
git clone https://github.com/YamiKnigth/pelican-github-sync.git githubsync
```

### 2. Ejecutar migraciones

```bash
cd /var/www/pelican
php artisan migrate
```

### 3. Limpiar caché

```bash
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear
```

### 4. Habilitar el plugin

Desde el panel de administración de Pelican:
1. Ve a **Admin** → **Plugins**
2. Busca **GithubSync**
3. Haz clic en **Enable**

## 📖 Uso

### Configurar por primera vez

1. Entra a un servidor en el panel
2. Ve a la sección de **Files**
3. En la parte superior verás el widget **GitHub Sync**
4. Haz clic en el icono de configuración (⚙️)
5. Completa el formulario:
   - **URL Repositorio**: `https://github.com/usuario/repo.git`
   - **Branch**: `main` (o la rama que prefieras)
   - **Usuario**: Tu nombre de usuario de GitHub
   - **Email**: Tu email de GitHub
   - **Token (PAT)**: [Crea un token de acceso personal](https://github.com/settings/tokens)

### Operaciones disponibles

- **Clone**: Clona el repositorio (solo la primera vez)
- **Pull**: Descarga cambios del repositorio
- **Push**: Sube cambios al repositorio

## 🔐 Token de Acceso Personal (PAT)

Para crear un PAT en GitHub:

1. Ve a https://github.com/settings/tokens
2. Click en **Generate new token** → **Generate new token (classic)**
3. Dale un nombre descriptivo
4. Selecciona los permisos:
   - ✅ `repo` (acceso completo a repositorios)
5. Genera el token y cópialo
6. Úsalo en la configuración del plugin

⚠️ **Importante**: El token se guarda encriptado en la base de datos.

## 🛠️ Desarrollo

### Estructura del proyecto

```
githubsync/
├── config/
│   └── githubsync.php              # Configuración
├── resources/
│   └── views/
│       └── toolbar.blade.php       # Vista del widget
├── src/
│   ├── Database/
│   │   └── Migrations/             # Migraciones de BD
│   ├── Filament/
│   │   └── Widgets/                # Widget de Filament
│   ├── Models/                     # Modelos Eloquent
│   ├── Providers/                  # Service Providers
│   ├── Services/                   # Lógica de negocio
│   └── GithubSyncPlugin.php        # Clase principal del plugin
└── plugin.json                      # Metadatos del plugin
```

## 🐛 Solución de problemas

### El widget no aparece

1. Verifica que el plugin esté habilitado en Admin → Plugins
2. Limpia la caché: `php artisan optimize:clear`
3. Verifica los logs: `tail -f /var/www/pelican/storage/logs/laravel.log`

### Error de migración

Si la tabla ya existe:
```bash
php artisan migrate:rollback --step=1
php artisan migrate
```

### Comandos Git no funcionan

- Verifica que el token tenga los permisos correctos
- Asegúrate de que la URL del repositorio sea correcta
- Revisa los logs de Wings

## 📝 Licencia

MIT

## 👤 Autor

**YamiKnigth**

## 🤝 Contribuir

Las contribuciones son bienvenidas. Por favor, abre un issue o un pull request.