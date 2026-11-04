# 👔 GESTIÓN DE COLABORADORES Y USUARIOS DE SISTEMA

> **Módulo:** Maestros / Personal  
> **Modelos:** `App\Models\User`, `App\Models\Colaborador`, `App\Models\Cargo`

---

## 👥 1. DIFERENCIA ENTRE USUARIO Y COLABORADOR

Eli Boutique distingue la identidad técnica de la identidad operativa:

* **Usuario (`users`):** Cuenta de autenticación en la aplicación web (email, password hash, tokens Sanctum, roles Spatie).
* **Colaborador (`colaboradores`):** Registro de la persona física que trabaja en la boutique (DNI, nombres, apellidos, teléfono, correo, cargo asignado).

---

## 🤝 2. ASIGNACIÓN AUTOMÁTICA EN VENTAS Y COMPRAS

Cuando un usuario autenticado realiza una operación en el sistema:
1. El servicio consulta si el email del usuario logueado (`auth()->user()->email`) coincide con el correo de un colaborador:
   ```php
   $user = auth()->user();
   $colaborador = Colaborador::where('correoColab', $user->email)->first();
   $colaboradorId = $colaborador ? $colaborador->id : null;
   ```
2. Al guardar la venta o la compra, se persiste `colaborador_id`.
3. Esto permite generar reportes de productividad y comisiones por vendedor sin que el cajero tenga que estar seleccionando manualmente su propio nombre en cada venta.
