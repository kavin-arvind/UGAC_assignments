from django.db import models
from django.contrib.auth.models import User, AbstractUser
import os

def user_directory_path(instance, filename):
    # Upload to a user's subdirectory in item_photos
    return os.path.join('item_photos', str(instance.user.id), filename)


class AuctionItem(models.Model):
    user = models.ForeignKey(User, on_delete=models.CASCADE)
    name = models.CharField(max_length=100)
    photo = models.ImageField(upload_to=user_directory_path)
    starting_bid = models.DecimalField(max_digits=10, decimal_places=2)
    is_bidding_open = models.BooleanField(default=True)  # Add this field
    description = models.CharField(default="",max_length=5000)
     # Winner user field
    winner = models.ForeignKey(User, on_delete=models.SET_NULL, null=True, blank=True, related_name='won_items')
    def __str__(self):
        return self.name


