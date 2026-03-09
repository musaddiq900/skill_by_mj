"""
Provider implementations for different LLM APIs.
Each provider handles authentication, request formatting, and response parsing.
"""

import json
from abc import ABC, abstractmethod
from typing import Dict, Any, Optional, List, Generator


class BaseProvider(ABC):
    """Base class for all LLM providers."""
    
    def __init__(self, api_key: str, config: Dict[str, Any] = None):
        self.api_key = api_key
        self.config = config or {}
    
    @abstractmethod
    def generate(self, prompt: str, **kwargs) -> str:
        """Generate a response for the given prompt."""
        pass
    
    @abstractmethod
    def generate_stream(self, prompt: str, **kwargs) -> Generator[str, None, None]:
        """Generate a streaming response for the given prompt."""
        pass


class GoogleProvider(BaseProvider):
    """Google AI Studio provider (Gemini)."""
    
    API_URL = "https://generativelanguage.googleapis.com/v1beta/models"
    
    def __init__(self, api_key: str, config: Dict[str, Any] = None):
        super().__init__(api_key, config)
        self.model = config.get("model", "gemini-2.0-flash")
    
    def generate(self, prompt: str, **kwargs) -> str:
        try:
            import google.generativeai as genai
            genai.configure(api_key=self.api_key)
            model = genai.GenerativeModel(self.model)
            response = model.generate_content(prompt)
            return response.text
        except ImportError:
            # Fallback to requests
            return self._generate_with_requests(prompt, **kwargs)
    
    def _generate_with_requests(self, prompt: str, **kwargs) -> str:
        import requests
        url = f"{self.API_URL}/{self.model}:generateContent?key={self.api_key}"
        data = {
            "contents": [{"parts": [{"text": prompt}]}],
            "generationConfig": {
                "temperature": kwargs.get("temperature", 0.7),
                "maxOutputTokens": kwargs.get("max_tokens", 2048),
            }
        }
        response = requests.post(url, json=data)
        response.raise_for_status()
        result = response.json()
        return result["candidates"][0]["content"]["parts"][0]["text"]
    
    def generate_stream(self, prompt: str, **kwargs) -> Generator[str, None, None]:
        import google.generativeai as genai
        genai.configure(api_key=self.api_key)
        model = genai.GenerativeModel(self.model)
        response = model.generate_content(prompt, stream=True)
        for chunk in response:
            if chunk.text:
                yield chunk.text


class GroqProvider(BaseProvider):
    """Groq provider for ultra-fast inference."""
    
    API_URL = "https://api.groq.com/openai/v1/chat/completions"
    
    def __init__(self, api_key: str, config: Dict[str, Any] = None):
        super().__init__(api_key, config)
        self.model = config.get("model", "llama-3.3-70b-versatile")
    
    def generate(self, prompt: str, **kwargs) -> str:
        import requests
        headers = {
            "Authorization": f"Bearer {self.api_key}",
            "Content-Type": "application/json",
        }
        data = {
            "model": self.model,
            "messages": [{"role": "user", "content": prompt}],
            "temperature": kwargs.get("temperature", 0.7),
            "max_tokens": kwargs.get("max_tokens", 2048),
        }
        response = requests.post(self.API_URL, headers=headers, json=data)
        response.raise_for_status()
        result = response.json()
        return result["choices"][0]["message"]["content"]
    
    def generate_stream(self, prompt: str, **kwargs) -> Generator[str, None, None]:
        import requests
        headers = {
            "Authorization": f"Bearer {self.api_key}",
            "Content-Type": "application/json",
        }
        data = {
            "model": self.model,
            "messages": [{"role": "user", "content": prompt}],
            "temperature": kwargs.get("temperature", 0.7),
            "max_tokens": kwargs.get("max_tokens", 2048),
            "stream": True,
        }
        response = requests.post(self.API_URL, headers=headers, json=data, stream=True)
        for line in response.iter_lines():
            if line:
                line = line.decode("utf-8")
                if line.startswith("data: ") and line != "data: [DONE]":
                    data = json.loads(line[6:])
                    content = data["choices"][0].get("delta", {}).get("content", "")
                    if content:
                        yield content


class OpenRouterProvider(BaseProvider):
    """OpenRouter provider for unified access to multiple models."""
    
    API_URL = "https://openrouter.ai/api/v1/chat/completions"
    
    def __init__(self, api_key: str, config: Dict[str, Any] = None):
        super().__init__(api_key, config)
        self.model = config.get("model", "deepseek/deepseek-r1")
    
    def generate(self, prompt: str, **kwargs) -> str:
        import requests
        headers = {
            "Authorization": f"Bearer {self.api_key}",
            "Content-Type": "application/json",
            "HTTP-Referer": "https://codex-cli.local",
            "X-Title": "Codex CLI",
        }
        data = {
            "model": self.model,
            "messages": [{"role": "user", "content": prompt}],
            "temperature": kwargs.get("temperature", 0.7),
            "max_tokens": kwargs.get("max_tokens", 2048),
        }
        response = requests.post(self.API_URL, headers=headers, json=data)
        response.raise_for_status()
        result = response.json()
        return result["choices"][0]["message"]["content"]
    
    def generate_stream(self, prompt: str, **kwargs) -> Generator[str, None, None]:
        import requests
        headers = {
            "Authorization": f"Bearer {self.api_key}",
            "Content-Type": "application/json",
            "HTTP-Referer": "https://codex-cli.local",
            "X-Title": "Codex CLI",
        }
        data = {
            "model": self.model,
            "messages": [{"role": "user", "content": prompt}],
            "temperature": kwargs.get("temperature", 0.7),
            "max_tokens": kwargs.get("max_tokens", 2048),
            "stream": True,
        }
        response = requests.post(self.API_URL, headers=headers, json=data, stream=True)
        for line in response.iter_lines():
            if line:
                line = line.decode("utf-8")
                if line.startswith("data: ") and line != "data: [DONE]":
                    data = json.loads(line[6:])
                    content = data["choices"][0].get("delta", {}).get("content", "")
                    if content:
                        yield content


class MistralProvider(BaseProvider):
    """Mistral AI provider."""
    
    API_URL = "https://api.mistral.ai/v1/chat/completions"
    
    def __init__(self, api_key: str, config: Dict[str, Any] = None):
        super().__init__(api_key, config)
        self.model = config.get("model", "codestral-latest")
    
    def generate(self, prompt: str, **kwargs) -> str:
        import requests
        headers = {
            "Authorization": f"Bearer {self.api_key}",
            "Content-Type": "application/json",
        }
        data = {
            "model": self.model,
            "messages": [{"role": "user", "content": prompt}],
            "temperature": kwargs.get("temperature", 0.7),
            "max_tokens": kwargs.get("max_tokens", 2048),
        }
        response = requests.post(self.API_URL, headers=headers, json=data)
        response.raise_for_status()
        result = response.json()
        return result["choices"][0]["message"]["content"]
    
    def generate_stream(self, prompt: str, **kwargs) -> Generator[str, None, None]:
        import requests
        headers = {
            "Authorization": f"Bearer {self.api_key}",
            "Content-Type": "application/json",
        }
        data = {
            "model": self.model,
            "messages": [{"role": "user", "content": prompt}],
            "temperature": kwargs.get("temperature", 0.7),
            "max_tokens": kwargs.get("max_tokens", 2048),
            "stream": True,
        }
        response = requests.post(self.API_URL, headers=headers, json=data, stream=True)
        for line in response.iter_lines():
            if line:
                line = line.decode("utf-8")
                if line.startswith("data: ") and line != "data: [DONE]":
                    data = json.loads(line[6:])
                    content = data["choices"][0].get("delta", {}).get("content", "")
                    if content:
                        yield content


class CerebrasProvider(BaseProvider):
    """Cerebras provider for ultra-fast inference."""
    
    API_URL = "https://api.cerebras.ai/v1/chat/completions"
    
    def __init__(self, api_key: str, config: Dict[str, Any] = None):
        super().__init__(api_key, config)
        self.model = config.get("model", "llama-3.3-70b")
    
    def generate(self, prompt: str, **kwargs) -> str:
        import requests
        headers = {
            "Authorization": f"Bearer {self.api_key}",
            "Content-Type": "application/json",
        }
        data = {
            "model": self.model,
            "messages": [{"role": "user", "content": prompt}],
            "temperature": kwargs.get("temperature", 0.7),
            "max_tokens": kwargs.get("max_tokens", 2048),
        }
        response = requests.post(self.API_URL, headers=headers, json=data)
        response.raise_for_status()
        result = response.json()
        return result["choices"][0]["message"]["content"]
    
    def generate_stream(self, prompt: str, **kwargs) -> Generator[str, None, None]:
        import requests
        headers = {
            "Authorization": f"Bearer {self.api_key}",
            "Content-Type": "application/json",
        }
        data = {
            "model": self.model,
            "messages": [{"role": "user", "content": prompt}],
            "temperature": kwargs.get("temperature", 0.7),
            "max_tokens": kwargs.get("max_tokens", 2048),
            "stream": True,
        }
        response = requests.post(self.API_URL, headers=headers, json=data, stream=True)
        for line in response.iter_lines():
            if line:
                line = line.decode("utf-8")
                if line.startswith("data: ") and line != "data: [DONE]":
                    data = json.loads(line[6:])
                    content = data["choices"][0].get("delta", {}).get("content", "")
                    if content:
                        yield content


class CohereProvider(BaseProvider):
    """Cohere provider for RAG and enterprise use cases."""
    
    API_URL = "https://api.cohere.ai/v1/chat"
    
    def __init__(self, api_key: str, config: Dict[str, Any] = None):
        super().__init__(api_key, config)
        self.model = config.get("model", "command-r-plus")
    
    def generate(self, prompt: str, **kwargs) -> str:
        import requests
        headers = {
            "Authorization": f"Bearer {self.api_key}",
            "Content-Type": "application/json",
        }
        data = {
            "model": self.model,
            "message": prompt,
            "temperature": kwargs.get("temperature", 0.7),
            "max_tokens": kwargs.get("max_tokens", 2048),
        }
        response = requests.post(self.API_URL, headers=headers, json=data)
        response.raise_for_status()
        result = response.json()
        return result["text"]
    
    def generate_stream(self, prompt: str, **kwargs) -> Generator[str, None, None]:
        # Cohere streaming via their SDK
        import cohere
        client = cohere.Client(self.api_key)
        stream = client.chat_stream(
            model=self.model,
            message=prompt,
            temperature=kwargs.get("temperature", 0.7),
            max_tokens=kwargs.get("max_tokens", 2048),
        )
        for event in stream:
            if event.event_type == "text-generation":
                yield event.text


class CloudflareProvider(BaseProvider):
    """Cloudflare Workers AI provider for edge deployment."""
    
    def __init__(self, api_key: str, config: Dict[str, Any] = None):
        super().__init__(api_key, config)
        self.account_id = config.get("account_id", "")
        self.model = config.get("model", "@cf/meta/llama-3.2-3b-instruct")
    
    def generate(self, prompt: str, **kwargs) -> str:
        import requests
        url = f"https://api.cloudflare.com/client/v4/accounts/{self.account_id}/ai/run/{self.model}"
        headers = {
            "Authorization": f"Bearer {self.api_key}",
            "Content-Type": "application/json",
        }
        data = {
            "messages": [{"role": "user", "content": prompt}],
            "temperature": kwargs.get("temperature", 0.7),
            "max_tokens": kwargs.get("max_tokens", 2048),
        }
        response = requests.post(url, headers=headers, json=data)
        response.raise_for_status()
        result = response.json()
        return result["result"]["response"]
    
    def generate_stream(self, prompt: str, **kwargs) -> Generator[str, None, None]:
        # Cloudflare doesn't support streaming yet, fallback to regular generation
        yield self.generate(prompt, **kwargs)


class GitHubProvider(BaseProvider):
    """GitHub Models provider."""
    
    API_URL = "https://models.inference.ai.azure.com/chat/completions"
    
    def __init__(self, api_key: str, config: Dict[str, Any] = None):
        super().__init__(api_key, config)
        self.model = config.get("model", "gpt-4o")
    
    def generate(self, prompt: str, **kwargs) -> str:
        import requests
        headers = {
            "Authorization": f"Bearer {self.api_key}",
            "Content-Type": "application/json",
        }
        data = {
            "model": self.model,
            "messages": [{"role": "user", "content": prompt}],
            "temperature": kwargs.get("temperature", 0.7),
            "max_tokens": kwargs.get("max_tokens", 2048),
        }
        response = requests.post(self.API_URL, headers=headers, json=data)
        response.raise_for_status()
        result = response.json()
        return result["choices"][0]["message"]["content"]
    
    def generate_stream(self, prompt: str, **kwargs) -> Generator[str, None, None]:
        import requests
        headers = {
            "Authorization": f"Bearer {self.api_key}",
            "Content-Type": "application/json",
        }
        data = {
            "model": self.model,
            "messages": [{"role": "user", "content": prompt}],
            "temperature": kwargs.get("temperature", 0.7),
            "max_tokens": kwargs.get("max_tokens", 2048),
            "stream": True,
        }
        response = requests.post(self.API_URL, headers=headers, json=data, stream=True)
        for line in response.iter_lines():
            if line:
                line = line.decode("utf-8")
                if line.startswith("data: ") and line != "data: [DONE]":
                    data = json.loads(line[6:])
                    content = data["choices"][0].get("delta", {}).get("content", "")
                    if content:
                        yield content


class NVIDIAProvider(BaseProvider):
    """NVIDIA NIM provider."""
    
    def __init__(self, api_key: str, config: Dict[str, Any] = None):
        super().__init__(api_key, config)
        self.model = config.get("model", "meta/llama-3.1-70b-instruct")
        self.base_url = config.get("base_url", "https://integrate.api.nvidia.com/v1")
    
    def generate(self, prompt: str, **kwargs) -> str:
        import requests
        url = f"{self.base_url}/chat/completions"
        headers = {
            "Authorization": f"Bearer {self.api_key}",
            "Content-Type": "application/json",
        }
        data = {
            "model": self.model,
            "messages": [{"role": "user", "content": prompt}],
            "temperature": kwargs.get("temperature", 0.7),
            "max_tokens": kwargs.get("max_tokens", 2048),
        }
        response = requests.post(url, headers=headers, json=data)
        response.raise_for_status()
        result = response.json()
        return result["choices"][0]["message"]["content"]
    
    def generate_stream(self, prompt: str, **kwargs) -> Generator[str, None, None]:
        import requests
        url = f"{self.base_url}/chat/completions"
        headers = {
            "Authorization": f"Bearer {self.api_key}",
            "Content-Type": "application/json",
        }
        data = {
            "model": self.model,
            "messages": [{"role": "user", "content": prompt}],
            "temperature": kwargs.get("temperature", 0.7),
            "max_tokens": kwargs.get("max_tokens", 2048),
            "stream": True,
        }
        response = requests.post(url, headers=headers, json=data, stream=True)
        for line in response.iter_lines():
            if line:
                line = line.decode("utf-8")
                if line.startswith("data: ") and line != "data: [DONE]":
                    data = json.loads(line[6:])
                    content = data["choices"][0].get("delta", {}).get("content", "")
                    if content:
                        yield content


class HuggingFaceProvider(BaseProvider):
    """HuggingFace Inference API provider."""
    
    def __init__(self, api_key: str, config: Dict[str, Any] = None):
        super().__init__(api_key, config)
        self.model = config.get("model", "mistralai/Mistral-7B-Instruct-v0.3")
    
    def generate(self, prompt: str, **kwargs) -> str:
        import requests
        url = f"https://api-inference.huggingface.co/models/{self.model}"
        headers = {
            "Authorization": f"Bearer {self.api_key}",
            "Content-Type": "application/json",
        }
        data = {
            "inputs": prompt,
            "parameters": {
                "temperature": kwargs.get("temperature", 0.7),
                "max_new_tokens": kwargs.get("max_tokens", 2048),
            }
        }
        response = requests.post(url, headers=headers, json=data)
        response.raise_for_status()
        result = response.json()
        if isinstance(result, list):
            return result[0]["generated_text"]
        return result.get("generated_text", "")
    
    def generate_stream(self, prompt: str, **kwargs) -> Generator[str, None, None]:
        # HF Inference API doesn't support streaming, fallback
        yield self.generate(prompt, **kwargs)


class AnthropicProvider(BaseProvider):
    """Anthropic Claude provider."""
    
    API_URL = "https://api.anthropic.com/v1/messages"
    
    def __init__(self, api_key: str, config: Dict[str, Any] = None):
        super().__init__(api_key, config)
        self.model = config.get("model", "claude-3-5-sonnet-20241022")
    
    def generate(self, prompt: str, **kwargs) -> str:
        import requests
        headers = {
            "x-api-key": self.api_key,
            "anthropic-version": "2023-06-01",
            "content-type": "application/json",
        }
        data = {
            "model": self.model,
            "max_tokens": kwargs.get("max_tokens", 2048),
            "messages": [{"role": "user", "content": prompt}],
        }
        response = requests.post(self.API_URL, headers=headers, json=data)
        response.raise_for_status()
        result = response.json()
        return result["content"][0]["text"]
    
    def generate_stream(self, prompt: str, **kwargs) -> Generator[str, None, None]:
        import requests
        headers = {
            "x-api-key": self.api_key,
            "anthropic-version": "2023-06-01",
            "content-type": "application/json",
        }
        data = {
            "model": self.model,
            "max_tokens": kwargs.get("max_tokens", 2048),
            "messages": [{"role": "user", "content": prompt}],
            "stream": True,
        }
        response = requests.post(self.API_URL, headers=headers, json=data, stream=True)
        for line in response.iter_lines():
            if line:
                line = line.decode("utf-8")
                if line.startswith("data: "):
                    data = json.loads(line[6:])
                    if data.get("type") == "content_block_delta":
                        yield data["delta"]["text"]


class OpenAIProvider(BaseProvider):
    """OpenAI provider."""
    
    API_URL = "https://api.openai.com/v1/chat/completions"
    
    def __init__(self, api_key: str, config: Dict[str, Any] = None):
        super().__init__(api_key, config)
        self.model = config.get("model", "gpt-4o")
    
    def generate(self, prompt: str, **kwargs) -> str:
        import requests
        headers = {
            "Authorization": f"Bearer {self.api_key}",
            "Content-Type": "application/json",
        }
        data = {
            "model": self.model,
            "messages": [{"role": "user", "content": prompt}],
            "temperature": kwargs.get("temperature", 0.7),
            "max_tokens": kwargs.get("max_tokens", 2048),
        }
        response = requests.post(self.API_URL, headers=headers, json=data)
        response.raise_for_status()
        result = response.json()
        return result["choices"][0]["message"]["content"]
    
    def generate_stream(self, prompt: str, **kwargs) -> Generator[str, None, None]:
        import requests
        headers = {
            "Authorization": f"Bearer {self.api_key}",
            "Content-Type": "application/json",
        }
        data = {
            "model": self.model,
            "messages": [{"role": "user", "content": prompt}],
            "temperature": kwargs.get("temperature", 0.7),
            "max_tokens": kwargs.get("max_tokens", 2048),
            "stream": True,
        }
        response = requests.post(self.API_URL, headers=headers, json=data, stream=True)
        for line in response.iter_lines():
            if line:
                line = line.decode("utf-8")
                if line.startswith("data: ") and line != "data: [DONE]":
                    data = json.loads(line[6:])
                    content = data["choices"][0].get("delta", {}).get("content", "")
                    if content:
                        yield content


# Provider factory
PROVIDER_MAP = {
    "google": GoogleProvider,
    "groq": GroqProvider,
    "openrouter": OpenRouterProvider,
    "mistral": MistralProvider,
    "cerebras": CerebrasProvider,
    "cohere": CohereProvider,
    "cloudflare": CloudflareProvider,
    "github": GitHubProvider,
    "nvidia": NVIDIAProvider,
    "huggingface": HuggingFaceProvider,
    "anthropic": AnthropicProvider,
    "openai": OpenAIProvider,
}


def get_provider(name: str, api_key: str, config: Dict[str, Any] = None) -> BaseProvider:
    """Get a provider instance by name."""
    if name not in PROVIDER_MAP:
        raise ValueError(f"Unknown provider: {name}")
    return PROVIDER_MAP[name](api_key, config)


def list_providers() -> List[str]:
    """List all available providers."""
    return list(PROVIDER_MAP.keys())
