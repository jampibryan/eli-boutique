---
name: project-readme
description: Analiza, genera y mantiene la documentación principal del proyecto en README.md de forma profesional, técnica, viva y sin exponer secretos ni inventar información.
---

# 📖 PROJECT README & SYSTEM DOCUMENTATION - SKILL

> **Activación:** `skill:project-readme`  
> **Propósito:** Protocolo estandarizado para auditar, estructurar, redactar y mantener viva la documentación principal del repositorio (**`README.md`**), garantizando exactitud técnica, ausencia de secretos y máxima utilidad para desarrolladores.

---

## 🎯 1. REGLA DE ORO: CERO SECRETOS Y CERO ALUCINACIÓN

- **NUNCA** copiar contraseñas de correos, llaves privadas ni claves de base de datos en el `README.md`.
- Toda configuración de credenciales debe documentarse con variables de entorno de ejemplo (`.env.example`).
- La documentación debe reflejar exactamente el código fuente existente en el repositorio.

---

## 📋 2. ESTRUCTURA CANÓNICA RECOMENDADA

1. **Título y Descripción:** Nombre del sistema y propósito de negocio (Boutique de ropa).
2. **Resumen de Tecnologías:** Stack backend (PHP 8.2 / Laravel 10 / Jetstream / Sanctum) y frontend (Vite / Tailwind / Bootstrap 5 / Livewire 3).
3. **Módulos Principales:** Ventas, Compras, Caja Diaria, Inventario por Tallas, Reportes.
4. **Requisitos del Sistema:** Versiones de PHP, extensiones requeridas (`gd`, `openssl`, `pdo_mysql`), Composer y Node.js.
5. **Instalación y Puesta en Marcha:** Comandos paso a paso (Sail / Docker y local).
6. **Variables de Entorno Clave:** Base de datos, Mail, y APIs externas.
7. **Flujos Transaccionales:** Diagrama de ventas, compras y caja.
8. **Seguridad y Permisos:** Roles de Spatie Permission y tokens Sanctum.
9. **Pruebas Automatizadas:** Comandos para correr la suite de pruebas.
