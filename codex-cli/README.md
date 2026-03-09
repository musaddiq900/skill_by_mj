# Codex CLI - Multi-LLM Command Line Tool

A powerful command-line interface for interacting with multiple LLM providers, inspired by OpenAI Codex.

## Features

- 🚀 **Multi-Provider Support**: Access 12+ LLM providers through a unified API
- ⚡ **Fast Inference**: Support for speed-critical providers like Groq and Cerebras
- 🔧 **Code Generation**: Optimized for coding tasks with specialized models
- 🌍 **Global Edge**: Low-latency access via Cloudflare Workers AI
- 📊 **Model Comparison**: Easy switching between providers for comparison
- 💰 **Cost Effective**: Free tier support across multiple providers

## Supported Providers

| Provider | Best Models | Key Limits | Best For |
|----------|-------------|------------|----------|
| **Google AI Studio** | Gemini 2.5 Pro/Flash, Gemma 3 | 5-15 RPM, 250K TPM, 250K tokens/day | Prototyping, multimodal apps, long-context tasks |
| **Groq** | Llama 3.3 70B, Llama 4 Scout, Qwen3 | 30 RPM, 1K req/day (70B models) | Speed-critical apps (300+ tokens/sec) |
| **OpenRouter** | DeepSeek R1, Llama 4, Qwen3, many open models | 20 RPM, 50 req/day (↑1K with $10 balance) | Model comparison, unified API access |
| **Mistral AI** | Mistral Large/Small, Codestral | 2 RPM, 1B tokens/month | Code generation, European data residency |
| **Cerebras** | Llama 3.3 70B, Qwen3 32B/235B | 30 RPM, 1M tokens/day | Agentic workflows, ultra-fast inference |
| **Cohere** | Command R+, Embed 4, Rerank 3.5 | 20 RPM, 1K req/month | RAG pipelines, search & retrieval |
| **Cloudflare Workers AI** | Llama 3.2, Mistral 7B, FLUX.2 | 10K neurons/day | Edge deployment, global low-latency apps |
| **GitHub Models** | GPT-4o, o3, DeepSeek-R1, Grok-3 | 10-15 RPM, 50-150 req/day | Quick testing, GitHub ecosystem integration |
| **NVIDIA NIM** | DeepSeek R1, Llama, Kimi K2.5 | 40 RPM, 1K credits on signup | Enterprise evaluation, self-hosted planning |
| **HuggingFace** | 300+ community models | Small monthly credits, cold starts | Specialized/niche models, research |
| **Anthropic** | Claude 3.5/3 Opus/Sonnet/Haiku | Varies by tier | Long context, reasoning tasks |
| **OpenAI** | GPT-4o, GPT-4 Turbo, o1, o3 | Varies by tier | General purpose, reliable API |

## Installation

### Prerequisites
- Python 3.8+
- pip package manager

### Install from source

```bash
git clone https://github.com/yourusername/codex-cli.git
cd codex-cli
pip install -e .
```

### Quick Install

```bash
pip install codex-cli
```

## Configuration

### 1. Initialize Configuration

```bash
codex init
```

This creates a `.codex` configuration file in your home directory.

### 2. Set API Keys

You can set API keys individually or use environment variables:

```bash
# Individual setup
codex config set --provider google --key YOUR_GOOGLE_API_KEY
codex config set --provider groq --key YOUR_GROQ_API_KEY
codex config set --provider openrouter --key YOUR_OPENROUTER_API_KEY
codex config set --provider mistral --key YOUR_MISTRAL_API_KEY
codex config set --provider cerebras --key YOUR_CEREBRAS_API_KEY
codex config set --provider cohere --key YOUR_COHERE_API_KEY
codex config set --provider cloudflare --key YOUR_CLOUDFLARE_API_KEY
codex config set --provider github --key YOUR_GITHUB_TOKEN
codex config set --provider nvidia --key YOUR_NVIDIA_API_KEY
codex config set --provider huggingface --key YOUR_HF_TOKEN
codex config set --provider anthropic --key YOUR_ANTHROPIC_API_KEY
codex config set --provider openai --key YOUR_OPENAI_API_KEY

# Or use environment variables
export CODEX_GOOGLE_API_KEY="your-key"
export CODEX_GROQ_API_KEY="your-key"
export CODEX_OPENROUTER_API_KEY="your-key"
# ... etc
```

### 3. Set Default Provider

```bash
codex config set-default --provider groq
```

## Usage

### Interactive Mode

```bash
codex chat
```

Start an interactive chat session with your default provider.

### Single Query

```bash
# Using default provider
codex ask "Write a Python function to sort a list"

# Specify provider
codex ask --provider groq "Explain quantum computing"

# Specify model
codex ask --model "llama-3.3-70b" "What is machine learning?"
```

### Code Generation

```bash
# Generate code from description
codex code "Create a REST API endpoint for user authentication"

# With specific language
codex code --lang python "Implement a binary search tree"

# With output file
codex code --output solution.py "Write a quicksort algorithm"
```

### File Analysis

```bash
# Analyze a code file
codex analyze ./src/main.py

# Get suggestions for improvement
codex review ./project/src
```

### Model Comparison

```bash
# Compare responses across providers
codex compare --providers groq,google,mistral "What is the capital of France?"
```

### List Available Models

```bash
# List all models from all providers
codex models

# List models from specific provider
codex models --provider google
```

### Streaming Mode

```bash
# Stream response token by token
codex ask --stream "Tell me a story about AI"
```

### Save Conversation

```bash
codex chat --save conversation.json
codex chat --load conversation.json
```

## Configuration File Format

The `.codex` config file (YAML format):

```yaml
providers:
  google:
    api_key: "your-google-key"
    default_model: "gemini-2.0-flash"
  groq:
    api_key: "your-groq-key"
    default_model: "llama-3.3-70b-versatile"
  openrouter:
    api_key: "your-openrouter-key"
    default_model: "deepseek/deepseek-r1"
  mistral:
    api_key: "your-mistral-key"
    default_model: "codestral-latest"
  cerebras:
    api_key: "your-cerebras-key"
    default_model: "llama-3.3-70b"
  cohere:
    api_key: "your-cohere-key"
    default_model: "command-r-plus"
  cloudflare:
    api_key: "your-cloudflare-key"
    account_id: "your-account-id"
    default_model: "@cf/meta/llama-3.2-3b-instruct"
  github:
    token: "your-github-token"
    default_model: "gpt-4o"
  nvidia:
    api_key: "your-nvidia-key"
    default_model: "meta/llama-3.1-70b-instruct"
  huggingface:
    token: "your-hf-token"
    default_model: "mistralai/Mistral-7B-Instruct-v0.3"
  anthropic:
    api_key: "your-anthropic-key"
    default_model: "claude-3-5-sonnet-20241022"
  openai:
    api_key: "your-openai-key"
    default_model: "gpt-4o"

default_provider: groq
settings:
  temperature: 0.7
  max_tokens: 2048
  timeout: 30
```

## Advanced Usage

### Custom System Prompts

```bash
codex ask --system "You are an expert Python developer" "How do I use decorators?"
```

### Temperature Control

```bash
codex ask --temperature 0.9 "Write a creative story"
codex ask --temperature 0.2 "Explain this code bug"
```

### Max Tokens

```bash
codex ask --max-tokens 4096 "Write a detailed explanation"
```

### Rate Limit Handling

```bash
# Automatic retry with exponential backoff
codex ask --retry 3 "Your query here"
```

### Batch Processing

```bash
# Process multiple prompts from a file
codex batch process prompts.txt --output results.json
```

## Environment Variables

| Variable | Description |
|----------|-------------|
| `CODEX_GOOGLE_API_KEY` | Google AI Studio API key |
| `CODEX_GROQ_API_KEY` | Groq API key |
| `CODEX_OPENROUTER_API_KEY` | OpenRouter API key |
| `CODEX_MISTRAL_API_KEY` | Mistral AI API key |
| `CODEX_CEREBRAS_API_KEY` | Cerebras API key |
| `CODEX_COHERE_API_KEY` | Cohere API key |
| `CODEX_CLOUDFLARE_API_KEY` | Cloudflare Workers AI key |
| `CODEX_CLOUDFLARE_ACCOUNT_ID` | Cloudflare account ID |
| `CODEX_GITHUB_TOKEN` | GitHub token for GitHub Models |
| `CODEX_NVIDIA_API_KEY` | NVIDIA NIM API key |
| `CODEX_HF_TOKEN` | HuggingFace token |
| `CODEX_ANTHROPIC_API_KEY` | Anthropic API key |
| `CODEX_OPENAI_API_KEY` | OpenAI API key |
| `CODEX_DEFAULT_PROVIDER` | Default provider name |
| `CODEX_CONFIG_PATH` | Custom config file path |

## Examples

### Quick Code Generation

```bash
$ codex code --lang python "Create a Flask app with JWT authentication"
```

### Multi-Provider Comparison

```bash
$ codex compare --providers groq,google,openai \
  "Explain the difference between async and parallel programming"
```

### Interactive Coding Session

```bash
$ codex chat --provider groq --model llama-3.3-70b
>>> Write a function to calculate fibonacci sequence
[Response streamed...]
>>> Now optimize it with memoization
[Response streamed...]
```

### Analyze Project

```bash
$ codex review ./my-project --output report.md
```

## Troubleshooting

### Common Issues

1. **API Key Not Found**
   ```bash
   codex config list  # Check configured keys
   codex config set --provider <name> --key <key>
   ```

2. **Rate Limit Exceeded**
   - Wait and retry, or switch to another provider
   - Use `--retry` flag for automatic retries

3. **Model Not Available**
   ```bash
   codex models --provider <name>  # Check available models
   ```

### Debug Mode

```bash
codex ask --debug "Your query"
```

## Contributing

Contributions are welcome! Please read our contributing guidelines before submitting PRs.

## License

MIT License - see LICENSE file for details.

## Acknowledgments

- Inspired by OpenAI Codex
- Thanks to all LLM providers for their amazing APIs
