from django.contrib import admin

# Register your models here.
from .models import AuctionItem

admin.site.register(AuctionItem)