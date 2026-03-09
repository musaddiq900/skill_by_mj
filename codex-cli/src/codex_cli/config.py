"""
Configuration management for Codex CLI.
Handles loading, saving, and managing API keys and settings.
"""

import os
import yaml
from pathlib import Path
from typing import Dict, Any, Optional


class Config:
    """Configuration manager for Codex CLI."""
    
    DEFAULT_CONFIG_PATH = Path.home() / ".codex"
    
    PROVIDERS = [
        "google",
        "groq", 
        "openrouter",
        "mistral",
        "cerebras",
        "cohere",
        "cloudflare",
        "github",
        "nvidia",
        "huggingface",
        "anthropic",
        "openai",
    ]
    
    ENV_KEY_MAP = {
        "google": "CODEX_GOOGLE_API_KEY",
        "groq": "CODEX_GROQ_API_KEY",
        "openrouter": "CODEX_OPENROUTER_API_KEY",
        "mistral": "CODEX_MISTRAL_API_KEY",
        "cerebras": "CODEX_CEREBRAS_API_KEY",
        "cohere": "CODEX_COHERE_API_KEY",
        "cloudflare": "CODEX_CLOUDFLARE_API_KEY",
        "github": "CODEX_GITHUB_TOKEN",
        "nvidia": "CODEX_NVIDIA_API_KEY",
        "huggingface": "CODEX_HF_TOKEN",
        "anthropic": "CODEX_ANTHROPIC_API_KEY",
        "openai": "CODEX_OPENAI_API_KEY",
    }
    
    DEFAULT_MODELS = {
        "google": "gemini-2.0-flash",
        "groq": "llama-3.3-70b-versatile",
        "openrouter": "deepseek/deepseek-r1",
        "mistral": "codestral-latest",
        "cerebras": "llama-3.3-70b",
        "cohere": "command-r-plus",
        "cloudflare": "@cf/meta/llama-3.2-3b-instruct",
        "github": "gpt-4o",
        "nvidia": "meta/llama-3.1-70b-instruct",
        "huggingface": "mistralai/Mistral-7B-Instruct-v0.3",
        "anthropic": "claude-3-5-sonnet-20241022",
        "openai": "gpt-4o",
    }
    
    def __init__(self, config_path: Optional[Path] = None):
        self.config_path = config_path or self._get_config_path()
        self.config: Dict[str, Any] = {}
        self.load()
    
    def _get_config_path(self) -> Path:
        """Get the configuration file path."""
        env_path = os.environ.get("CODEX_CONFIG_PATH")
        if env_path:
            return Path(env_path)
        return self.DEFAULT_CONFIG_PATH
    
    def load(self) -> None:
        """Load configuration from file."""
        if self.config_path.exists():
            with open(self.config_path, "r") as f:
                self.config = yaml.safe_load(f) or {}
        else:
            self.config = {
                "providers": {},
                "default_provider": "groq",
                "settings": {
                    "temperature": 0.7,
                    "max_tokens": 2048,
                    "timeout": 30,
                }
            }
    
    def save(self) -> None:
        """Save configuration to file."""
        self.config_path.parent.mkdir(parents=True, exist_ok=True)
        with open(self.config_path, "w") as f:
            yaml.safe_dump(self.config, f, default_flow_style=False)
    
    def get_api_key(self, provider: str) -> Optional[str]:
        """Get API key for a provider."""
        # First check environment variable
        env_key = self.ENV_KEY_MAP.get(provider)
        if env_key:
            env_value = os.environ.get(env_key)
            if env_value:
                return env_value
        
        # Then check config file
        providers = self.config.get("providers", {})
        provider_config = providers.get(provider, {})
        return provider_config.get("api_key") or provider_config.get("token")
    
    def set_api_key(self, provider: str, api_key: str) -> None:
        """Set API key for a provider."""
        if "providers" not in self.config:
            self.config["providers"] = {}
        if provider not in self.config["providers"]:
            self.config["providers"][provider] = {}
        
        key_name = "token" if provider in ["github", "huggingface"] else "api_key"
        self.config["providers"][provider][key_name] = api_key
        self.save()
    
    def get_default_provider(self) -> str:
        """Get the default provider."""
        return self.config.get("default_provider", "groq")
    
    def set_default_provider(self, provider: str) -> None:
        """Set the default provider."""
        if provider not in self.PROVIDERS:
            raise ValueError(f"Unknown provider: {provider}")
        self.config["default_provider"] = provider
        self.save()
    
    def get_setting(self, key: str, default: Any = None) -> Any:
        """Get a setting value."""
        settings = self.config.get("settings", {})
        return settings.get(key, default)
    
    def set_setting(self, key: str, value: Any) -> None:
        """Set a setting value."""
        if "settings" not in self.config:
            self.config["settings"] = {}
        self.config["settings"][key] = value
        self.save()
    
    def get_default_model(self, provider: str) -> str:
        """Get the default model for a provider."""
        providers = self.config.get("providers", {})
        provider_config = providers.get(provider, {})
        if "default_model" in provider_config:
            return provider_config["default_model"]
        return self.DEFAULT_MODELS.get(provider, "")
    
    def list_providers(self) -> list:
        """List all configured providers."""
        return list(self.config.get("providers", {}).keys())
    
    def is_provider_configured(self, provider: str) -> bool:
        """Check if a provider has an API key configured."""
        return self.get_api_key(provider) is not None
