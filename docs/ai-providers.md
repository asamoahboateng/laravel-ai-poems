# AI Chat Providers

This application supports multiple AI providers. Configure your preferred provider by adding its API key to your `.env` file.

---

## OpenAI

The most widely used AI provider, offering GPT models.

**Setup:**
1. Create an account at [platform.openai.com](https://platform.openai.com)
2. Generate an API key under **API Keys**
3. Add to `.env`:
   ```
   OPENAI_API_KEY=sk-...
   ```

**Available Models:**

| Model | Description |
|-------|-------------|
| `gpt-4o-mini` | Fast, affordable small model for lightweight tasks |
| `gpt-4o` | High-intelligence flagship model for complex tasks |
| `gpt-4.1` | Latest flagship model with improved coding and instruction following |
| `gpt-4.1-mini` | Balanced performance and cost |
| `gpt-4.1-nano` | Fastest and most cost-effective |
| `o3-mini` | Reasoning model optimized for STEM and coding |

**Pricing:** Pay-per-token. See [openai.com/pricing](https://openai.com/pricing)

---

## Anthropic

Creator of the Claude model family, known for safety and long-context capabilities.

**Setup:**
1. Create an account at [console.anthropic.com](https://console.anthropic.com)
2. Generate an API key under **API Keys**
3. Add to `.env`:
   ```
   ANTHROPIC_API_KEY=sk-ant-...
   ```

**Available Models:**

| Model | Description |
|-------|-------------|
| `claude-sonnet-4-5-20250929` | Balanced intelligence and speed |
| `claude-haiku-4-5-20251001` | Fastest Claude model, great for simple tasks |
| `claude-opus-4-6` | Most capable model for complex reasoning |

**Pricing:** Pay-per-token. See [anthropic.com/pricing](https://anthropic.com/pricing)

---

## Google Gemini

Google's multimodal AI models with large context windows.

**Setup:**
1. Visit [aistudio.google.com](https://aistudio.google.com)
2. Create an API key
3. Add to `.env`:
   ```
   GEMINI_API_KEY=AI...
   ```

**Available Models:**

| Model | Description |
|-------|-------------|
| `gemini-2.0-flash` | Fast and versatile, good balance of speed and quality |
| `gemini-2.0-flash-lite` | Lightweight variant for high-volume tasks |
| `gemini-2.5-pro-preview-06-05` | Most capable Gemini model with advanced reasoning |

**Pricing:** Free tier available. See [ai.google.dev/pricing](https://ai.google.dev/pricing)

---

## Groq

Ultra-fast inference platform running open-source models on custom LPU hardware.

**Setup:**
1. Create an account at [console.groq.com](https://console.groq.com)
2. Generate an API key
3. Add to `.env`:
   ```
   GROQ_API_KEY=gsk_...
   ```

**Available Models:**

| Model | Description |
|-------|-------------|
| `llama-3.3-70b-versatile` | Meta's Llama 3.3 70B, strong general-purpose model |
| `llama-3.1-8b-instant` | Smaller Llama model, extremely fast responses |
| `mixtral-8x7b-32768` | Mistral's mixture-of-experts model with 32K context |

**Pricing:** Free tier available with rate limits. See [groq.com/pricing](https://groq.com/pricing)

---

## xAI

Creators of the Grok model family.

**Setup:**
1. Create an account at [console.x.ai](https://console.x.ai)
2. Generate an API key
3. Add to `.env`:
   ```
   XAI_API_KEY=xai-...
   ```

**Available Models:**

| Model | Description |
|-------|-------------|
| `grok-3` | Flagship model with strong reasoning capabilities |
| `grok-3-mini` | Smaller, faster variant for everyday tasks |

**Pricing:** Pay-per-token. See [x.ai/api](https://x.ai/api)

---

## DeepSeek

Chinese AI lab known for high-performance open-source models.

**Setup:**
1. Create an account at [platform.deepseek.com](https://platform.deepseek.com)
2. Generate an API key
3. Add to `.env`:
   ```
   DEEPSEEK_API_KEY=sk-...
   ```

**Available Models:**

| Model | Description |
|-------|-------------|
| `deepseek-chat` | DeepSeek V3, general-purpose chat model |
| `deepseek-reasoner` | DeepSeek R1, chain-of-thought reasoning model |

**Pricing:** Very competitive pricing. See [platform.deepseek.com/api-docs/pricing](https://platform.deepseek.com/api-docs/pricing)

---

## Mistral

European AI company offering efficient, multilingual models.

**Setup:**
1. Create an account at [console.mistral.ai](https://console.mistral.ai)
2. Generate an API key
3. Add to `.env`:
   ```
   MISTRAL_API_KEY=...
   ```

**Available Models:**

| Model | Description |
|-------|-------------|
| `mistral-large-latest` | Most capable Mistral model for complex tasks |
| `mistral-small-latest` | Efficient model for straightforward tasks |

**Pricing:** Pay-per-token. See [mistral.ai/technology](https://mistral.ai/technology)

---

## Ollama

Run open-source models locally on your machine. No API key required.

**Setup:**
1. Install Ollama from [ollama.com/download](https://ollama.com/download)
2. Pull a model:
   ```bash
   ollama pull llama3.2
   ollama pull mistral
   ollama pull phi3
   ```
3. Ollama runs automatically on `http://localhost:11434`. To customize:
   ```
   OLLAMA_BASE_URL=http://localhost:11434
   ```

**Available Models:**

| Model | Description |
|-------|-------------|
| `llama3.2` | Meta's Llama 3.2, good general-purpose local model |
| `mistral` | Mistral 7B, efficient and fast |
| `phi3` | Microsoft's Phi-3, compact but capable |

Any model available on [ollama.com/library](https://ollama.com/library) can be pulled and used.

**Pricing:** Free (runs locally). Requires sufficient RAM/GPU.

---

## LM Studio

Run any GGUF model locally with a GUI. Exposes an OpenAI-compatible API.

**Setup:**
1. Download LM Studio from [lmstudio.ai](https://lmstudio.ai)
2. Download a model through the LM Studio interface
3. Start the local server (Developer tab > Start Server)
4. The server runs on `http://localhost:1234/v1` by default. To customize:
   ```
   LM_STUDIO_BASE_URL=http://localhost:1234/v1
   ```

**Available Models:**

| Model | Description |
|-------|-------------|
| `default` | Whatever model is currently loaded in LM Studio |

Load any GGUF-format model through the LM Studio UI. Popular choices include Llama, Mistral, Phi, and Qwen variants.

**Pricing:** Free (runs locally). Requires sufficient RAM/GPU.

---

## Quick Start

1. Copy `.env.example` to `.env` if you haven't already
2. Add at least one provider's API key
3. Log in to the application
4. Navigate to **Chat** from the navbar
5. Select your provider and model from the sidebar dropdowns
6. Start chatting
