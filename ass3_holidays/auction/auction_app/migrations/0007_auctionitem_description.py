# Generated by Django 4.2.4 on 2023-08-23 11:36

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('auction_app', '0006_auctionitem_winner_delete_user'),
    ]

    operations = [
        migrations.AddField(
            model_name='auctionitem',
            name='description',
            field=models.CharField(default='', max_length=5000),
        ),
    ]
