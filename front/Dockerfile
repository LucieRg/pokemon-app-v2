# Utiliser une image Node.js pour le développement
FROM node:18 AS development

# Définir le répertoire de travail
WORKDIR /app

# Copier les fichiers package.json et package-lock.json
COPY package*.json ./

# Installer les dépendances
RUN npm install

# Copier le reste des fichiers de l'application
COPY . .

# Exposer le port 4200 pour ng serve
EXPOSE 4200

# Lancer l'application Angular en mode développement avec ng serve
CMD ["npm", "start"]
