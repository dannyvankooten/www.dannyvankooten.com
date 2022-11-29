import pandas as pd

kwh_per_gb_low = 0.06 # fixed broadband
kwh_per_gb_high = 0.81  # mobile 4G
monthly_unique_visitors = 200e6 * 0.1 # assuming 10% of monthly visitors is unique
daily_unique_visitors = monthly_unique_visitors / 30
day_in_seconds = 3600 * 24
household_energy_usage_per_month = 2800 / 12 # kWh

def human_readable_size(size):
    if size > 1e12:
        return "{:d} TB".format(round(size/1e12))
    elif size > 1e9:
        return "{:d} GB".format(round(size/1e9))
    elif size > 1e6:
        return "{:d} MB".format(round(size/1e6))
    else:
        return "{:d} kB".format(round(size/1e3))


df = pd.read_csv('output.csv')
df['expires'].fillna(0, inplace=True)
df['combined_savings'] = df['js_savings'] + df['css_savings'] + df['html_savings']
df['relative_saving'] = df['combined_savings'] / df['total_size']
df = df[df['combined_savings'] >= 0]
df = df[0:500]
df.sort_values(['combined_savings'], inplace=True, ascending=False)

print("\n\nSize (in bytes): ")
print(df['total_size'].describe(percentiles=[.1, .25, .5, .75, .90]).to_markdown())

print("\n\nSavings: ")
print(df[['html_savings', 'css_savings', 'js_savings', 'combined_savings']].describe(percentiles=[.1, .25, .5, .75, .90]).to_markdown())
print()

print("\n\nCache lifetimes: ")
df['expires_hours'] = round(df['expires'] / 3600)
print(df[['expires', 'expires_hours']].describe(percentiles=[.1, .25, .5, .75, .90]).to_markdown())
print()

total_savings = df['combined_savings'].sum()
print("Total saving (across al {} websites) by applying better minification (after GZIP compression): {}".format(df.shape[0], human_readable_size(total_savings)))

# because median cache lifetime seems to be 1 day, multiply by daily unique here
total = df['combined_savings'].median() * daily_unique_visitors
total_gb = total / 1e9
total_kwh_per_gb_low = total_gb * kwh_per_gb_low * 365
total_kwh_per_gb_high = total_gb * kwh_per_gb_high * 365
print("50% of websites could save about {} of data tx per day".format(human_readable_size(total), df.shape[0]))
print("Which equals between {:0.0f} kWh and {:0.0f} kWh per year".format(total_kwh_per_gb_low, total_kwh_per_gb_high))

print("\nTop 10 worst offenders: (values are in bytes)")
print(df.head(10))

