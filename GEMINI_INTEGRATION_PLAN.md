# 🤖 Plan de Integración de Inteligencia Artificial (Google Gemini) en Vitakee

> **Versión:** 1.0 | **Fecha:** Marzo 2026 | **Plataforma:** Vitakee Health Platform  
> **Tecnología base:** PHP (MVC), MySQL, JavaScript — Stack actual del sistema

---

## 📋 Tabla de Contenidos

1. [Visión General](#1-visión-general)
2. [Módulos con Integración de IA](#2-módulos-con-integración-de-ia)
   - 2.1 [Análisis Inteligente de Biomarcadores](#21-análisis-inteligente-de-biomarcadores)
   - 2.2 [Asistente Virtual de Salud (Chatbot IA)](#22-asistente-virtual-de-salud-chatbot-ia)
   - 2.3 [IA para el Especialista (Dashboard Inteligente)](#23-ia-para-el-especialista-dashboard-inteligente)
   - 2.4 [Resumen Automático de Consultas y Segunda Opinión](#24-resumen-automático-de-consultas-y-segunda-opinión)
   - 2.5 [Análisis de Documentos de Laboratorio (Vision + IA)](#25-análisis-de-documentos-de-laboratorio-vision--ia)
   - 2.6 [Recomendaciones Personalizadas de Estilo de Vida](#26-recomendaciones-personalizadas-de-estilo-de-vida)
   - 2.7 [Detección de Anomalías y Alertas Predictivas](#27-detección-de-anomalías-y-alertas-predictivas)
   - 2.8 [IA para Administradores (Insights de Plataforma)](#28-ia-para-administradores-insights-de-plataforma)
   - 2.9 [Transcripción y Notas de Videollamadas](#29-transcripción-y-notas-de-videollamadas)
   - 2.10 [Búsqueda Inteligente de Especialistas](#210-búsqueda-inteligente-de-especialistas)
3. [Arquitectura Técnica](#3-arquitectura-técnica)
4. [Requisitos Técnicos](#4-requisitos-técnicos)
5. [Fases de Implementación](#5-fases-de-implementación)
6. [Estimado de Costos](#6-estimado-de-costos)
7. [Consideraciones Éticas y Legales](#7-consideraciones-éticas-y-legales)
8. [Referencias de APIs](#8-referencias-de-apis)

---

## 1. Visión General

Vitakee es una plataforma de salud que conecta a **usuarios pacientes** con **especialistas médicos**. Los usuarios registran datos de biomarcadores (perfil lipídico, composición corporal, función renal, metabolismo energético), solicitan segundas opiniones, y se comunican con especialistas via chat y videollamada.

La integración de **Google Gemini** (a través de **Google AI Studio** / **Vertex AI**) permitirá:

- Interpretar resultados de salud en lenguaje claro y personalizado para el usuario.
- Asistir al especialista con herramientas de apoyo a la decisión clínica.
- Automatizar tareas repetitivas de documentación y comunicación.
- Brindar alertas predictivas basadas en tendencias de datos.
- Elevar la percepción de valor del servicio Vitakee con funcionalidades de última generación.

> **Ventaja clave de Gemini:** Integración nativa con el ecosistema de Google (Google Cloud, Google Meet, Google Calendar) que Vitakee ya utiliza para autenticación y videollamadas, lo que simplifica la arquitectura y reduce fricciones de configuración.

---

## 2. Módulos con Integración de IA

---

### 2.1 Análisis Inteligente de Biomarcadores

**Módulos afectados:** `user_lipid_profile`, `user_body_composition`, `user_renal_function`, `user_energy_metabolism`

**¿Qué hace?**
Al guardar o visualizar un registro de biomarcador, la IA genera automáticamente una **interpretación médica en lenguaje natural**, adaptada al idioma del usuario (ES / EN).

**Ejemplo de output (Perfil Lipídico):**
> *"Tu colesterol LDL de 148 mg/dL está ligeramente elevado. Considera reducir el consumo de grasas saturadas. Tu HDL de 52 mg/dL está en un rango saludable, lo que es positivo para tu salud cardiovascular. Te recomendamos consultar tu próximo examen en 3 meses."*

**Modelo Gemini:** `gemini-2.0-flash` (rápido y costo-eficiente para textos médicos cortos)

**Implementación técnica:**

```php
// app/services/GeminiService.php  (NUEVO)
class GeminiService {
    private string $apiKey;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct() {
        $this->apiKey = $_ENV['GEMINI_API_KEY'];
    }

    public function analyzeBiomarker(string $type, array $values, string $lang = 'EN'): string {
        $prompt = $this->buildBiomarkerPrompt($type, $values, $lang);
        return $this->callAPI($prompt, 'gemini-2.0-flash');
    }

    public function callAPI(string $prompt, string $model = 'gemini-2.0-flash', int $maxTokens = 300): string {
        $url = "{$this->baseUrl}/{$model}:generateContent?key={$this->apiKey}";

        $payload = json_encode([
            'contents' => [[
                'parts' => [['text' => $prompt]]
            ]],
            'generationConfig' => [
                'maxOutputTokens' => $maxTokens,
                'temperature'     => 0.4,
            ]
        ]);

        $response = file_get_contents($url, false, stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/json\r\n",
                'content' => $payload,
            ]
        ]));

        $data = json_decode($response, true);
        return $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
    }
}
```

**Archivos a modificar:**
| Archivo | Cambio |
|---|---|
| `app/services/GeminiService.php` | **[NUEVO]** Servicio central de IA |
| `app/controllers/LipidProfileController.php` | Llamar a IA al guardar/obtener registro |
| `app/controllers/BodyCompositionController.php` | Ídem |
| `app/controllers/RenalFunctionController.php` | Ídem |
| `app/controllers/EnergyMetabolismController.php` | Ídem |
| `app/views/user_lipid_profile_component.php` | Mostrar sección "Interpretación IA" |
| `.env` | Agregar `GEMINI_API_KEY=AIza...` |

---

### 2.2 Asistente Virtual de Salud (Chatbot IA)

**Módulo afectado:** `chat.php`

**¿Qué hace?**
Un chatbot inteligente disponible 24/7 para los **usuarios**. Puede responder dudas de salud general, explicar resultados de sus biomarcadores, orientar sobre cuándo consultar a un especialista, y guiar el uso de la plataforma.

> ⚠️ El chatbot **no diagnostica ni prescribe**. Incluye disclaimers claros.

**Características:**
- Contexto del historial reciente del usuario (últimos biomarcadores).
- Multilingüe (ES/EN según `$_SESSION['idioma']`).
- Memoria de conversación persistente con **Gemini Chat Sessions**.
- Opción de **escalar al especialista** si el tema es complejo.

**Modelo Gemini:** `gemini-2.0-flash` con `system instruction` médica especializada.

**Implementación técnica:**

```php
// En app/controllers/ChatAIController.php  (NUEVO)
public function sendMessage() {
    $userMessage  = $_POST['message'];
    $history      = $this->getChatHistory(); // desde BD ai_chat_sessions
    $userBioData  = $this->getUserLatestBiomarkers();

    $systemInstruction = "Eres Vita, un asistente de salud de la plataforma Vitakee. 
    Orientas a los usuarios sobre sus datos de salud de forma empática y clara. 
    No diagnosticas ni prescribes medicamentos. 
    Datos del usuario: " . json_encode($userBioData) . "
    Idioma: " . ($_SESSION['idioma'] ?? 'EN');

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$this->apiKey}";

    $payload = [
        'system_instruction' => ['parts' => [['text' => $systemInstruction]]],
        'contents' => array_merge(
            $history,  // historial previo [{"role":"user","parts":[...]}, ...]
            [['role' => 'user', 'parts' => [['text' => $userMessage]]]]
        ),
        'generationConfig' => ['maxOutputTokens' => 400, 'temperature' => 0.5]
    ];

    $response = $this->gemini->callAPIRaw($url, $payload);

    // Guardar en BD
    $this->saveChatMessage('user', $userMessage);
    $this->saveChatMessage('model', $response);

    echo json_encode(['value' => true, 'message' => $response]);
}
```

> **Nota:** Gemini usa `"role": "model"` en lugar de `"role": "assistant"` para las respuestas del asistente.

**Archivos a modificar:**
| Archivo | Cambio |
|---|---|
| `app/controllers/ChatAIController.php` | **[NUEVO]** Controlador chatbot IA |
| `app/views/chat.php` | Agregar pestaña/panel "Asistente Vita IA" |
| `app/Router.php` | Registrar ruta `/ai-chat` |

---

### 2.3 IA para el Especialista (Dashboard Inteligente)

**Módulo afectado:** `dashboard_specialists.php`, `SpecialistDashboardController.php`

**¿Qué hace?**
El especialista recibe un **resumen inteligente** de cada paciente antes de una consulta, incluyendo:

1. **Resumen ejecutivo del paciente** — tendencias de biomarcadores, alertas.
2. **Sugerencias de preguntas clínicas** — qué explorar en la consulta.
3. **Comparativa con valores de referencia** — resaltando anomalías.
4. **Puntuación de riesgo cardiovascular/renal** generada por IA.

**Modelo Gemini:** `gemini-2.5-pro` (modelo más potente para razonamiento clínico complejo)

**Implementación técnica:**

```php
// Genera un briefing clínico previo a la consulta
public function generatePatientBriefing(string $patientId): string {
    $lipid   = $this->getLipidHistory($patientId);
    $body    = $this->getBodyCompositionHistory($patientId);
    $renal   = $this->getRenalHistory($patientId);
    $energy  = $this->getEnergyHistory($patientId);

    $prompt = "Eres un asistente médico clínico. 
    Genera un resumen conciso (máx. 400 palabras) del estado de salud del paciente
    basándote en sus últimos datos de biomarcadores. Identifica tendencias, anomalías
    y sugiere 3 preguntas clave para la próxima consulta.
    Datos: " . json_encode(compact('lipid','body','renal','energy'));

    return $this->gemini->callAPI($prompt, 'gemini-2.5-pro', 600);
}
```

**Archivos a modificar:**
| Archivo | Cambio |
|---|---|
| `app/controllers/SpecialistDashboardController.php` | Agregar método `generatePatientBriefing()` |
| `app/views/dashboard_specialists.php` | Panel "Resumen IA del Paciente" en ficha de consulta |
| `app/views/specialist_service_requests.php` | Botón "Ver Resumen IA" por cada solicitud |

---

### 2.4 Resumen Automático de Consultas y Segunda Opinión

**Módulo afectado:** `SecondOpinionRequestsController.php`, `user_expert_review.php`

**¿Qué hace?**
- Al finalizar una segunda opinión, la IA genera un **resumen estructurado** del caso.
- Ayuda al especialista a redactar su opinión con una **plantilla inteligente** pre-llenada.
- Genera un **documento PDF-friendly** con el resumen para que el usuario lo descargue.

**Modelo Gemini:** `gemini-2.5-pro`

**Implementación técnica:**

```php
// Resumen automático para segunda opinión
public function generateSecondOpinionSummary(string $requestId): string {
    $request  = $this->getRequestDetails($requestId);
    $userData = $this->getUserBiomarkers($request['user_id']);

    $prompt = "Eres un asistente médico experto. Genera un resumen clínico 
    estructurado para una segunda opinión médica. Incluye: (1) Motivo de consulta, 
    (2) Datos relevantes de salud, (3) Análisis preliminar, (4) Puntos a considerar 
    por el especialista. Usa formato con secciones claras.
    Datos del caso: " . json_encode(array_merge($request, $userData));

    return $this->gemini->callAPI($prompt, 'gemini-2.5-pro', 700);
}
```

**Archivos a modificar:**
| Archivo | Cambio |
|---|---|
| `app/controllers/SecondOpinionRequestsController.php` | Método `generateSecondOpinionSummary()` |
| `app/views/user_expert_review.php` | Sección "Resumen Generado por IA" |

---

### 2.5 Análisis de Documentos de Laboratorio (Vision + IA)

**Módulo afectado:** `TestDocumentsController.php`, `user_test_documents.php`

**¿Qué hace?**
Cuando el usuario sube una imagen/PDF de un resultado de laboratorio, la IA:
1. **Extrae los valores numéricos** con Gemini Vision (nativo en todos los modelos Gemini).
2. **Auto-llena los campos** del formulario de biomarcadores.
3. **Genera una interpretación** del resultado.

> **Ventaja Gemini:** La capacidad multimodal (imágenes, PDFs) está integrada nativamente en todos los modelos, sin necesidad de un modelo separado.

**Modelo Gemini:** `gemini-2.0-flash` con input de imagen

**Implementación técnica:**

```php
// Analizar imagen de resultado de laboratorio
public function analyzeLabDocument(string $imagePath): array {
    $imageBase64 = base64_encode(file_get_contents($imagePath));
    $mimeType    = mime_content_type($imagePath); // image/jpeg, image/png, application/pdf

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$this->apiKey}";

    $payload = [
        'contents' => [[
            'parts' => [
                ['text' => 'Extrae los valores de laboratorio de esta imagen. 
                 Devuelve únicamente un JSON válido con los campos encontrados:
                 ldl, hdl, triglycerides, total_cholesterol, creatinine, glucose, 
                 uric_acid, weight, height, bmi. Solo incluye los valores presentes.'],
                ['inline_data' => [
                    'mime_type' => $mimeType,
                    'data'      => $imageBase64
                ]]
            ]
        ]],
        'generationConfig' => ['maxOutputTokens' => 500, 'temperature' => 0.1]
    ];

    $response = $this->gemini->callAPIRaw($url, $payload);
    // Limpiar posible markdown de la respuesta (```json ... ```)
    $json = preg_replace('/```json\s*|\s*```/', '', $response);
    return json_decode($json, true) ?? [];
}
```

**Archivos a modificar:**
| Archivo | Cambio |
|---|---|
| `app/controllers/TestDocumentsController.php` | Método `analyzeLabDocument()` |
| `app/views/user_test_documents.php` | Botón "Analizar con IA" al subir documento |

---

### 2.6 Recomendaciones Personalizadas de Estilo de Vida

**Módulo afectado:** `dashboard.php`, `UserController.php`

**¿Qué hace?**
En el dashboard del usuario, la IA genera **recomendaciones personalizadas semanales** de:
- Alimentación según su perfil lipídico y composición corporal.
- Actividad física según su metabolismo energético y IMC.
- Hidratación y hábitos según función renal.
- Recordatorios de próximos chequeos sugeridos.

**Modelo Gemini:** `gemini-2.0-flash`

**Implementación técnica:**

```php
public function generateWeeklyRecommendations(string $userId): string {
    $profile = $this->getAllUserBiomarkers($userId);
    $lang    = $_SESSION['idioma'] ?? 'EN';

    $prompt = "Eres un coach de salud personalizado. Basándote en estos datos,
    genera 4 recomendaciones concretas y motivadoras para esta semana:
    1 alimentación, 1 ejercicio, 1 hidratación/hábitos, 1 recordatorio médico.
    Sé específico, usa lenguaje positivo y empático. Idioma: {$lang}.
    Datos: " . json_encode($profile);

    return $this->gemini->callAPI($prompt, 'gemini-2.0-flash', 400);
}
```

**Archivos a modificar:**
| Archivo | Cambio |
|---|---|
| `app/controllers/UserController.php` | Método `generateWeeklyRecommendations()` + cache (24h) |
| `app/views/dashboard.php` | Widget "Recomendaciones Vita IA de la Semana" |

---

### 2.7 Detección de Anomalías y Alertas Predictivas

**Módulo afectado:** `NotificationController.php`, `BiomarkerController.php`

**¿Qué hace?**
Cuando se guarda un nuevo registro de biomarcador, la IA analiza la **tendencia histórica** y activa automáticamente una notificación si detecta:
- Incremento sostenido del colesterol LDL en las últimas 3 mediciones.
- Deterioro de la función renal (creatinina en ascenso).
- IMC fuera de rango con tendencia negativa.
- Glucosa con valores limítrofes repetidos.

**Modelo Gemini:** `gemini-2.0-flash` (análisis rápido)

**Implementación técnica:**

```php
public function detectAnomalies(string $type, array $history): ?string {
    if (count($history) < 2) return null; // Necesita al menos 2 registros

    $prompt = "Analiza la tendencia de estos registros de {$type}. 
    Si detectas alguna anomalía, alerta o tendencia preocupante, 
    devuelve un mensaje de alerta breve (máx. 100 palabras) para notificar al usuario.
    Si todo está bien, responde exactamente con la palabra: OK
    Datos ordenados por fecha: " . json_encode($history);

    $result = trim($this->gemini->callAPI($prompt, 'gemini-2.0-flash', 150));
    return ($result === 'OK') ? null : $result;
}
```

**Archivos a modificar:**
| Archivo | Cambio |
|---|---|
| `app/controllers/NotificationController.php` | Agregar tipo `ai_anomaly_alert` |
| `app/controllers/BiomarkerController.php` | Disparar análisis tras cada guardado |
| Base de datos | Nueva columna `notification_source ENUM('system','ai')` |

---

### 2.8 IA para Administradores (Insights de Plataforma)

**Módulo afectado:** `AdministratorController.php`, dashboard de administrador

**¿Qué hace?**
El administrador recibe un **reporte semanal generado por IA** con:
- Análisis de comportamiento de usuarios.
- Tendencias en tipos de consultas más solicitadas.
- Detección de especialistas con alta/baja actividad.
- Recomendaciones de marketing o mejora de plataforma.

**Modelo Gemini:** `gemini-2.5-pro`

```php
public function generateAdminInsights(): string {
    $stats = [
        'total_users'        => $this->getTotalUsers(),
        'total_specialists'  => $this->getTotalSpecialists(),
        'top_requests'       => $this->getTopRequestTypes(),
        'inactive_users'     => $this->getInactiveUsers30Days(),
        'revenue_week'       => $this->getWeeklyRevenue(),
        'avg_rating'         => $this->getAvgSpecialistRating(),
    ];

    $prompt = "Eres un analista de negocio para una plataforma de salud digital.
    Analiza estos KPIs semanales y genera: (1) 3 observaciones clave,
    (2) 2 alertas si hay métricas preocupantes, (3) 2 recomendaciones accionables.
    Datos: " . json_encode($stats);

    return $this->gemini->callAPI($prompt, 'gemini-2.5-pro', 600);
}
```

**Archivos a modificar:**
| Archivo | Cambio |
|---|---|
| `app/controllers/AdministratorController.php` | Método `generateAdminInsights()` |
| `app/views/` (admin dashboard) | Widget "Análisis Inteligente de Plataforma" |

---

### 2.9 Transcripción y Notas de Videollamadas

**Módulo afectado:** `VideoCallsController.php`, `video_call.php`

**¿Qué hace?**
Usando la **API de Speech-to-Text de Google Cloud** (integración natural con Gemini en el ecosistema Google), se transcribe el audio de las videollamadas y luego Gemini genera:
- Un **resumen de la consulta** con los puntos clave discutidos.
- Una lista de **próximos pasos / indicaciones** dadas por el especialista.
- El resumen queda disponible para ambas partes.

> **Ventaja Gemini:** Dado que Vitakee ya usa Google Cloud (Google Auth, Google Meet), la integración con **Google Cloud Speech-to-Text** es más directa usando las mismas credenciales de servicio. También es posible enviar audio directamente a Gemini 2.0 Flash para transcripción.

**Modelo Gemini:** `gemini-2.0-flash` (audio nativo) + `gemini-2.5-pro` (resumen)

```php
public function transcribeAndSummarize(string $audioFilePath, string $lang): array {
    // Opción A: Gemini 2.0 Flash con audio nativo (hasta 9.5h)
    $audioBase64 = base64_encode(file_get_contents($audioFilePath));
    $transcript  = $this->gemini->transcribeAudio($audioBase64, $lang);

    // Resumir con Gemini 2.5 Pro
    $prompt = "Eres un asistente médico. Resume esta transcripción de consulta 
    médica en: (1) Motivos de consulta, (2) Hallazgos del especialista, 
    (3) Indicaciones y próximos pasos, (4) Seguimiento recomendado.
    Transcripción: " . $transcript;

    $summary = $this->gemini->callAPI($prompt, 'gemini-2.5-pro', 700);

    return ['transcript' => $transcript, 'summary' => $summary];
}
```

**Archivos a modificar:**
| Archivo | Cambio |
|---|---|
| `app/controllers/VideoCallsController.php` | Método `transcribeAndSummarize()` |
| `app/views/video_call.php` | Botón "Generar Resumen IA" al finalizar |
| Base de datos | Nueva tabla `video_call_summaries` |

---

### 2.10 Búsqueda Inteligente de Especialistas

**Módulo afectado:** `SpecialistController.php`, landing/búsqueda de especialistas

**¿Qué hace?**
El usuario describe en lenguaje natural lo que necesita (ej. *"Busco un nutricionista para bajar de peso con hipertensión"*) y la IA:
1. Interpreta la intención.
2. Mapea a especialidades y características.
3. Filtra y rankea especialistas disponibles de la base de datos.

**Modelo Gemini:** `gemini-2.0-flash` (extracción de intención)

```php
public function intelligentSearch(string $userQuery): array {
    $prompt = "Extrae de esta búsqueda de salud los filtros relevantes.
    Devuelve únicamente un JSON válido con: specialty (string), 
    keywords (array), conditions (array).
    Búsqueda: '{$userQuery}'";

    $raw     = $this->gemini->callAPI($prompt, 'gemini-2.0-flash', 150);
    $json    = preg_replace('/```json\s*|\s*```/', '', $raw);
    $filters = json_decode($json, true) ?? [];

    return $this->specialistRepository->searchByFilters($filters);
}
```

---

## 3. Arquitectura Técnica

```
vitakee-users/
├── app/
│   ├── services/
│   │   └── GeminiService.php          ← [NUEVO] Servicio central IA
│   ├── controllers/
│   │   ├── ChatAIController.php       ← [NUEVO] Chatbot Vita
│   │   ├── LipidProfileController.php ← [MODIFICAR]
│   │   ├── BiomarkerController.php    ← [MODIFICAR]
│   │   └── ...
│   └── views/
│       ├── dashboard.php              ← [MODIFICAR] - Widget IA
│       ├── chat.php                   ← [MODIFICAR] - Panel Vita IA
│       └── ...
├── .env                               ← Agregar GEMINI_API_KEY
└── composer.json                      ← Agregar google/generative-ai-php (opcional)
```

### Flujo de datos

```
Usuario/Especialista
        │
        ▼
  Vista PHP (UI)
        │ AJAX / Form Submit
        ▼
  Controlador PHP
        │ llama a
        ▼
  GeminiService.php ──── HTTPS ────► Google AI API
        │                              (gemini-2.0-flash / gemini-2.5-pro)
        ▼
  Respuesta estructurada
        │
        ▼
  Base de datos (cache opcional)
        │
        ▼
  Vista renderizada
```

### Cache de respuestas IA

Para reducir costos, implementar cache en MySQL:

```sql
CREATE TABLE ai_response_cache (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    cache_key    VARCHAR(64) UNIQUE NOT NULL,   -- MD5(prompt)
    response     TEXT NOT NULL,
    model        VARCHAR(50),
    tokens_used  INT,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at   TIMESTAMP NOT NULL              -- TTL configurable
);
```

---

## 4. Requisitos Técnicos

### 4.1 Dependencias PHP

```bash
# Opción A: Librería oficial de Google (recomendada)
composer require google/generative-ai-php

# Opción B: Sin dependencias externas (cURL nativo PHP)
# El GeminiService.php usa file_get_contents / cURL directamente
```

### 4.2 Variables de entorno (`.env`)

```env
# Google Gemini
GEMINI_API_KEY=AIzaSy-xxxxxxxxxxxxxxxxxxxxxxxx
GEMINI_DEFAULT_MODEL=gemini-2.0-flash
GEMINI_PRO_MODEL=gemini-2.5-pro
GEMINI_TIMEOUT=30                            # segundos
AI_CACHE_TTL=86400                           # 24 horas en segundos
AI_ENABLED=true                              # Feature flag global
```

### 4.3 Configuración del servidor

| Requisito | Valor recomendado |
|---|---|
| `allow_url_fopen` | `On` |
| `max_execution_time` | `≥ 60s` (para audio multimodal) |
| `upload_max_filesize` | `≥ 25MB` (para documentos LAB) |
| `post_max_size` | `≥ 25MB` |
| PHP Version | `≥ 8.1` |
| HTTPS | **Obligatorio** (para enviar datos médicos) |

### 4.4 Base de datos — Cambios necesarios

```sql
-- Tabla para almacenar interpretaciones IA de biomarcadores
CREATE TABLE ai_biomarker_interpretations (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    user_id         VARCHAR(36) NOT NULL,
    record_id       VARCHAR(36) NOT NULL,
    biomarker_type  ENUM('lipid','body_composition','renal','energy') NOT NULL,
    interpretation  TEXT NOT NULL,
    model_used      VARCHAR(50),
    tokens_used     INT,
    lang            CHAR(2) DEFAULT 'EN',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla para historial del chatbot IA
-- Nota: Gemini usa "model" como rol (no "assistant")
CREATE TABLE ai_chat_sessions (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     VARCHAR(36) NOT NULL,
    role        ENUM('user','model','system') NOT NULL,
    content     TEXT NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id)
);

-- Tabla para resúmenes de videollamadas
CREATE TABLE video_call_summaries (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    video_call_id   VARCHAR(36) NOT NULL,
    transcript      LONGTEXT,
    summary         TEXT,
    next_steps      TEXT,
    lang            CHAR(2) DEFAULT 'EN',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 4.5 Obtener API Key de Google Gemini

1. Ir a [https://aistudio.google.com](https://aistudio.google.com).
2. Iniciar sesión con la cuenta de Google del proyecto (la misma de Google Cloud).
3. Clic en **"Get API key"** → **"Create API key"**.
4. Seleccionar el proyecto de Google Cloud existente de Vitakee.
5. Copiar la clave en `.env` → `GEMINI_API_KEY`.
6. Configurar **límites de gasto** en Google Cloud Console → *APIs & Services → Quotas*.

> **Ventaja:** Al usar el mismo proyecto de Google Cloud que ya tiene configurado para Google Auth y Google Meet, no se necesita crear un nuevo proyecto ni configurar facturación desde cero.

---

## 5. Fases de Implementación

### Fase 1 — Fundamentos (Semana 1-2)
- [ ] Crear `GeminiService.php` con métodos base.
- [ ] Agregar `GEMINI_API_KEY` al `.env`.
- [ ] Crear tabla `ai_response_cache` en BD.
- [ ] Implementar análisis de biomarcadores (módulo más visible).
- [ ] Testing con datos reales en Google AI Studio Playground.

### Fase 2 — Chatbot Vita (Semana 3-4)
- [ ] Crear `ChatAIController.php`.
- [ ] Integrar panel de chat IA en `chat.php`.
- [ ] Crear tabla `ai_chat_sessions` (con rol `model` en lugar de `assistant`).
- [ ] System instruction engineering para el asistente "Vita".

### Fase 3 — Herramientas para Especialistas (Semana 5-6)
- [ ] Resumen inteligente de pacientes en dashboard especialista.
- [ ] Resumen automático de segunda opinión.
- [ ] Búsqueda inteligente de especialistas.

### Fase 4 — Funciones Avanzadas (Semana 7-8)
- [ ] OCR de documentos de laboratorio (Vision nativo de Gemini).
- [ ] Alertas predictivas y detección de anomalías.
- [ ] Recomendaciones semanales personalizadas.

### Fase 5 — Admin & Videollamadas (Semana 9-10)
- [ ] Insights de plataforma para admin.
- [ ] Transcripción con Gemini 2.0 Flash audio nativo o Google Speech-to-Text.
- [ ] Monitoreo de costos y optimización de tokens.

---

## 6. Estimado de Costos

> Precios de Google Gemini API vigentes a **marzo 2026**. Verificar en [https://ai.google.dev/pricing](https://ai.google.dev/pricing)

### Precios de modelos (Gemini API — Google AI Studio)

| Modelo | Input (por 1M tokens) | Output (por 1M tokens) | Uso sugerido |
|---|---|---|---|
| `gemini-2.5-pro` | $1.25 (≤200K ctx) / $2.50 (>200K) | $10.00 / $15.00 | Análisis clínicos complejos, resúmenes |
| `gemini-2.0-flash` | $0.10 | $0.40 | Análisis básico, chatbot, búsqueda, OCR |
| `gemini-2.0-flash` (audio) | $0.70 / 1M tokens audio | — | Transcripción de videollamadas |
| **Free Tier** | **1,500 req/día gratis** | — | Ideal para fase de pruebas |

> 🎁 **Ventaja importante:** Gemini ofrece un **Free Tier generoso** (1,500 solicitudes/día con `gemini-2.0-flash`) perfecto para pruebas y primeros usuarios sin costo alguno.

### Estimado mensual por escenario

#### 🟢 Escenario Inicial (100 usuarios activos/mes)

| Función | Llamadas/mes | Tokens aprox. | Modelo | Costo estimado |
|---|---|---|---|---|
| Análisis biomarcadores | 500 | 150K | gemini-2.0-flash | **$0.07** |
| Chatbot Vita | 2,000 msg | 600K | gemini-2.0-flash | **$0.28** |
| Análisis especialistas | 100 | 80K | gemini-2.5-pro | **$0.90** |
| Resúmenes 2da opinión | 50 | 75K | gemini-2.5-pro | **$0.85** |
| Recomendaciones | 400 | 120K | gemini-2.0-flash | **$0.06** |
| Alertas predictivas | 200 | 30K | gemini-2.0-flash | **$0.01** |
| **TOTAL** | | | | **≈ $2.17/mes** |

> 💡 Con el Free Tier, este escenario podría ser **completamente gratuito** durante los primeros meses.

#### 🟡 Escenario Medio (500 usuarios activos/mes)

| Función | Llamadas/mes | Tokens aprox. | Modelo | Costo estimado |
|---|---|---|---|---|
| Análisis biomarcadores | 2,500 | 750K | gemini-2.0-flash | **$0.35** |
| Chatbot Vita | 10,000 msg | 3M | gemini-2.0-flash | **$1.40** |
| Análisis especialistas | 500 | 400K | gemini-2.5-pro | **$4.50** |
| Resúmenes 2da opinión | 250 | 375K | gemini-2.5-pro | **$4.22** |
| OCR documentos lab | 300 | 150K+imgs | gemini-2.0-flash | **$0.80** |
| Recomendaciones | 2,000 | 600K | gemini-2.0-flash | **$0.28** |
| Alertas predictivas | 1,000 | 150K | gemini-2.0-flash | **$0.07** |
| Transcripción videollamadas | 100 llamadas x 20min | ~4M tok audio | gemini-2.0-flash | **$2.80** |
| Admin insights | 4 reportes | 20K | gemini-2.5-pro | **$0.23** |
| **TOTAL** | | | | **≈ $14.65/mes** |

#### 🔴 Escenario Escalado (2,000 usuarios activos/mes)

| Función | Costo estimado |
|---|---|
| Análisis biomarcadores | $1.40 |
| Chatbot Vita | $5.60 |
| Análisis especialistas | $18.00 |
| Resúmenes 2da opinión | $16.88 |
| OCR documentos lab | $3.20 |
| Recomendaciones personalizadas | $1.12 |
| Alertas predictivas | $0.28 |
| Transcripción videollamadas | $11.20 |
| Admin insights | $0.50 |
| **TOTAL** | **≈ $58.18/mes** |

### Comparativa con OpenAI

| Escenario | Gemini | OpenAI | Ahorro con Gemini |
|---|---|---|---|
| 🟢 Inicial (100 usuarios) | $2.17 | $2.76 | ~21% más barato |
| 🟡 Medio (500 usuarios) | $14.65 | $29.50 | ~50% más barato |
| 🔴 Escalado (2,000 usuarios) | $58.18 | $117.45 | ~50% más barato |

### Estrategias para reducir costos

| Estrategia | Ahorro estimado |
|---|---|
| Cache de respuestas (24h TTL) | 30-40% |
| Usar `gemini-2.0-flash` por defecto (solo escalar a `gemini-2.5-pro` para clínicos) | 60-70% en tokens |
| Aprovechar el Free Tier para usuarios de prueba | 100% en primeros meses |
| Limitar historial del chatbot a últimos 8 mensajes | 20% |
| Comprimir prompt con datos mínimos necesarios | 15-25% |
| Rate limiting por usuario (máx. 20 consultas IA/día) | Variable |

---

## 7. Consideraciones Éticas y Legales

> ⚠️ **Importante: Vitakee maneja datos de salud sensibles.**

### Disclaimers obligatorios

Agregar en toda interfaz de IA:

```
"Esta información es generada por Inteligencia Artificial y tiene
carácter orientativo. No sustituye el criterio de un profesional
de la salud calificado. Consulta siempre con un especialista."
```

### Privacidad de datos (HIPAA / GDPR)

- **No enviar** nombres, apellidos, documentos de identidad ni emails a la API de Gemini.
- Usar solo datos anonimizados (UUIDs internos + valores numéricos de biomarcadores).
- Revisar la [política de privacidad de Google AI](https://ai.google.dev/gemini-api/terms) y desactivar el uso de datos para entrenamiento en Google AI Studio → **Settings → Data Collection**.
- Si se usa **Vertex AI** (Google Cloud) en lugar de AI Studio, los datos **no se usan para entrenamiento** por defecto, con mayor garantía de privacidad empresarial.
- Actualizar la **Política de Privacidad** de Vitakee para mencionar el uso de IA de Google.
- Agregar consentimiento explícito del usuario para el análisis con IA.

### Vertex AI vs. Google AI Studio

| Aspecto | Google AI Studio (API Key) | Vertex AI (Google Cloud) |
|---|---|---|
| Configuración | Simple, API Key | Requiere cuenta GCP + credenciales |
| Privacidad de datos | Datos pueden usarse para mejorar modelos | **Datos NO se usan para entrenamiento** |
| SLA / Uptime | Estándar | Enterprise (99.9%+) |
| Recomendado para | Desarrollo, pruebas, MVP | **Producción con datos médicos** |
| Precio | Mismo modelo | Mismo modelo |

> **Recomendación:** Empezar con **Google AI Studio** para desarrollo y migrar a **Vertex AI** cuando se lance a producción con usuarios reales.

### Limitaciones de la IA médica

- La IA **no diagnostica** ni **prescribe**.
- Los resultados pueden contener errores; siempre requieren revisión humana.
- Definir claramente en términos de uso la responsabilidad de Vitakee.

---

## 8. Referencias de APIs

| Recurso | URL |
|---|---|
| Google AI Studio | [https://aistudio.google.com](https://aistudio.google.com) |
| Documentación Gemini API | [https://ai.google.dev/docs](https://ai.google.dev/docs) |
| Precios actualizados | [https://ai.google.dev/pricing](https://ai.google.dev/pricing) |
| Librería PHP oficial | [https://github.com/google-gemini/generative-ai-php](https://github.com/google-gemini/generative-ai-php) |
| Vertex AI (producción) | [https://cloud.google.com/vertex-ai/generative-ai](https://cloud.google.com/vertex-ai/generative-ai) |
| Playground (pruebas) | [https://aistudio.google.com/prompts/new_chat](https://aistudio.google.com/prompts/new_chat) |
| Google Cloud Console | [https://console.cloud.google.com](https://console.cloud.google.com) |

---

*Documento generado para Vitakee Health Platform — Marzo 2026*  
*Actualizar precios y modelos de Gemini conforme evolucione la API de Google.*
