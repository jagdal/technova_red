# Frontend React (Vite) — multi-stage
FROM node:20-alpine AS build

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY index.html vite.config.js eslint.config.js ./
COPY src ./src

RUN npm run build

FROM nginx:alpine

COPY docker/nginx-react.conf /etc/nginx/conf.d/default.conf
COPY --from=build /app/dist /usr/share/nginx/html

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
