# Use the official WordPress image as the base image
FROM wordpress:6.4.3

# Install mPDF library
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    libfontconfig1 \
    libxrender1 \
    libjpeg62-turbo \
    libfreetype6 \
    libpng16-16 \
    && rm -rf /var/lib/apt/lists/*

RUN mkdir -p /var/www/html/wp-content/plugins/mpdf && \
    curl -SL "https://github.com/mpdf/mpdf/archive/v8.0.10.tar.gz" | tar -xz -C /var/www/html/wp-content/plugins/mpdf --strip-components=1
