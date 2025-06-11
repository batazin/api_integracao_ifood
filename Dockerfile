# Use uma imagem base oficial do PHP com Apache.
# Ajuste a versão do PHP (ex: 8.1, 8.2, 8.3) conforme a necessidade da sua aplicação.
FROM php:8.2-apache

# Define o diretório de trabalho.
WORKDIR /var/www/html

# Instalar dependências do sistema necessárias para extensões comuns e Composer.
# Adicione ou remova pacotes conforme a necessidade da sua aplicação.
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP.
# Descomente e adicione as extensões que sua aplicação precisa.
# Exemplo:
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql zip intl opcache
# Para mysqli:
# RUN docker-php-ext-install mysqli
# Para outras extensões como soap, xsl:
# RUN docker-php-ext-install soap xsl

# Instalar Composer globalmente.
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar os arquivos do Composer primeiro para aproveitar o cache do Docker.
# Se o seu composer.lock não estiver presente, você pode remover `composer.lock*`
COPY ifood-integration/composer.json ifood-integration/composer.lock* ./

# Instalar dependências do Composer.
# --no-dev: para não instalar dependências de desenvolvimento em produção.
# --optimize-autoloader: para otimizar o autoloader do Composer.
# --no-interaction, --no-plugins, --no-scripts: podem ser úteis dependendo do seu setup.
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copia todo o conteúdo da pasta ifood-integration para o Apache
COPY ifood-integration/. /var/www/html/
COPY ifood-integration/src/config/ /var/www/html/config/
COPY ifood-integration/src/controllers/ /var/www/html/controllers/
COPY ifood-integration/src/index.php /var/www/html/index.php
COPY ifood-integration/src/endpoints/ /var/www/html/src/endpoints/
COPY ifood-integration/src/services/ /var/www/html/services/
COPY ifood-integration/src/services/AuthService.php /var/www/html/services/AuthService.php
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# A porta padrão do Apache é 80.
EXPOSE 80

# O comando padrão da imagem php:apache já inicia o Apache



